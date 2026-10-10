/* Search and generate BSE shortcodes locally. Content search uses authenticated requests; no saved preferences. */
(() => {
  'use strict';
  const data = window.BSE_TOOLS;
  const choice = document.getElementById('bse-use-case');
  if (!data || !choice) return;
  const fields = document.getElementById('bse-fields');
  const output = document.getElementById('bse-generated');
  const error = document.getElementById('bse-generator-error');
  const copyGenerated = document.getElementById('bse-copy-generated');
  const status = document.getElementById('bse-copy-status');
  const drafts = new Map();
  let activeTag = '';
  let rendering = false;
  let searchTimer;
  let searchController;
  let searchSequence = 0;
  const picker = document.getElementById('bse-content-picker');
  const searchQuery = document.getElementById('bse-content-query');
  const searchResults = document.getElementById('bse-content-results');
  const searchStatus = document.getElementById('bse-content-status');
  function generate() {
    if (rendering) return;
    const entry = data.catalog[activeTag];
    let message = '';
    const attributes = [];
    for (const field of entry.fields) {
      const input = document.getElementById(`bse-field-${field.key}`);
      const value = field.type === 'text' ? input.value : input.value.trim();
      const required = field.key === 'id';
      input.removeAttribute('aria-invalid');
      if (required && !/^[1-9]\d*$/.test(value)) message = data.i18n.required;
      else if (field.type === 'number' && value && !/^[1-9]\d*$/.test(value)) message = data.i18n.invalid;
      else if (/["'\[\]\r\n]/.test(value)) message = data.i18n.quote;
      if (message) { input.setAttribute('aria-invalid', 'true'); break; }
      // Empty fields use the shortcode's own defaults. Select defaults are omitted.
      if (value && !(field.type === 'select' && value === field.default)) {
        attributes.push(`${field.key}="${value}"`);
      }
    }
    error.textContent = message;
    error.hidden = !message;
    copyGenerated.disabled = !!message;
    if (!picker.hidden && !searchQuery.value) { const idInput = document.getElementById('bse-field-id') || document.getElementById('bse-field-post_id'); searchStatus.textContent = idInput.value ? `${data.i18n.selectedItem} ${idInput.value}` : data.i18n.currentItem; }
    output.value = message ? '' : `[${activeTag}${attributes.length ? ' ' + attributes.join(' ') : ''}]`;
  }
  function remember() {
    if (!activeTag) return;
    drafts.set(activeTag, Object.fromEntries(data.catalog[activeTag].fields.map(field => [field.key, document.getElementById(`bse-field-${field.key}`).value])));
  }
  function renderFields() {
    rendering = true;
    clearTimeout(searchTimer); searchController?.abort(); searchSequence++;
    searchResults.replaceChildren(); searchStatus.textContent = ''; searchQuery.value = '';
    remember();
    activeTag = choice.value;
    const entry = data.catalog[activeTag];
    document.getElementById('bse-case-description').textContent = entry.description;
    fields.replaceChildren();
    for (const field of entry.fields) {
      const row = document.createElement('div');
      row.className = 'bse-field';
      const label = document.createElement('label');
      label.htmlFor = `bse-field-${field.key}`;
      label.textContent = field.label;
      const input = document.createElement(field.type === 'select' ? 'select' : 'input');
      input.id = label.htmlFor;
      if (field.type === 'select') {
        for (const [value, title] of Object.entries(field.options)) {
          const option = document.createElement('option'); option.value = value; option.textContent = title; input.append(option);
        }
      } else {
        input.type = field.type;
        if (field.type === 'number') { input.min = '1'; input.step = '1'; }
        if (field.key === 'id') { input.required = true; input.setAttribute('aria-describedby', error.id); }
      }
      input.value = drafts.get(activeTag)?.[field.key] ?? field.default;
      input.addEventListener('input', generate);
      input.addEventListener('change', generate);
      row.append(label, input); fields.append(row);
    }
    picker.hidden = !entry.fields.some(field => ['id', 'post_id'].includes(field.key));
    rendering = false;
    generate();
  }
  async function copy(text) {
    status.textContent = '';
    try {
      if (!navigator.clipboard?.writeText) throw new Error('manual');
      await navigator.clipboard.writeText(text);
      status.textContent = data.i18n.copied;
    } catch (_) {
      const fallback = document.createElement('textarea'); fallback.value = text;
      fallback.setAttribute('aria-label', choice.labels[0].textContent);
      status.append(fallback); fallback.focus(); fallback.select();
      try {
        if (document.execCommand('copy')) { status.textContent = data.i18n.copied; return; }
      } catch (_) { /* Keep the selected text available. */ }
      const instruction = document.createElement('span'); instruction.textContent = data.i18n.manual; status.append(instruction);
    }
  }
  searchQuery.addEventListener('input', () => {
    clearTimeout(searchTimer); searchController?.abort();
    const sequence = ++searchSequence;
    searchResults.replaceChildren(); searchStatus.textContent = '';
    const query = searchQuery.value.trim();
    if (query.length < 2) return;
    searchTimer = setTimeout(async () => {
      searchController = new AbortController();
      searchStatus.textContent = data.i18n.searching;
      try {
        const url = new URL(data.ajaxUrl, location.href);
        url.searchParams.set('action', 'bse_content_search'); url.searchParams.set('nonce', data.nonce); url.searchParams.set('q', query);
        const response = await fetch(url, { credentials: 'same-origin', signal: searchController.signal });
        if (!response.ok) throw new Error('search');
        const result = await response.json();
        if (sequence !== searchSequence) return;
        if (!result.success || !Array.isArray(result.data)) throw new Error('search');
        searchStatus.textContent = result.data.length ? '' : data.i18n.noContent;
        for (const item of result.data) {
          const button = document.createElement('button'); button.type = 'button'; button.className = 'button';
          button.textContent = `${item.title || '#'+item.id} — ${item.type} · ${item.status} · #${item.id}`;
          button.addEventListener('click', () => {
            const input = document.getElementById('bse-field-id') || document.getElementById('bse-field-post_id');
            input.value = String(item.id); searchResults.replaceChildren(); searchQuery.value = ''; generate(); input.focus();
          });
          searchResults.append(button);
        }
      } catch (err) { if (err.name !== 'AbortError' && sequence === searchSequence) searchStatus.textContent = data.i18n.searchFailed; }
    }, 300);
  });
  choice.addEventListener('change', renderFields);
  copyGenerated.addEventListener('click', () => { if (!copyGenerated.disabled) copy(output.value); });
  document.querySelectorAll('.bse-copy').forEach(button => button.addEventListener('click', () => copy(button.previousElementSibling.textContent)));
  document.querySelectorAll('.bse-configure').forEach(button => button.addEventListener('click', () => {
    choice.value = button.dataset.tag; renderFields(); choice.focus();
    document.querySelector('.bse-generator').scrollIntoView({ behavior: 'smooth', block: 'start' });
  }));
  document.getElementById('bse-search').addEventListener('input', event => {
    const query = event.target.value.trim().toLocaleLowerCase();
    let matches = 0;
    document.querySelectorAll('.bse-card').forEach(card => {
      card.hidden = !card.textContent.toLocaleLowerCase().includes(query);
      if (!card.hidden) matches++;
    });
    document.querySelectorAll('.bse-group').forEach(group => { group.hidden = !group.querySelector('.bse-card:not([hidden])'); });
    document.getElementById('bse-no-results').hidden = matches !== 0;
  });
  document.querySelectorAll('[data-bse-document]').forEach(link => link.addEventListener('click', event => {
    const dialog = document.getElementById(`bse-document-${link.dataset.bseDocument}`);
    if (!dialog || !dialog.showModal) return;
    event.preventDefault();
    const trigger = event.currentTarget;
    dialog.addEventListener('close', () => trigger.focus(), { once: true });
    dialog.showModal();
  }));
  document.querySelectorAll('[data-bse-close]').forEach(button => button.addEventListener('click', () => button.closest('dialog').close()));
  renderFields();
})();
