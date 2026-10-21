/* Native documentation dialogs with real-link and native-form fallbacks. */
(() => {
  'use strict';
  if (window.BSE_DIALOGS_READY) return;
  window.BSE_DIALOGS_READY = true;
  const native = window.HTMLDialogElement?.prototype;
  const triggers = new WeakMap();
  const dialogs = document.querySelectorAll('.bse-root dialog.bse-document-dialog');
  function close(dialog) {
    try { if (native?.close) native.close.call(dialog); } catch (_) { /* Native form remains available. */ }
  }
  dialogs.forEach(dialog => {
    dialog.hidden = !dialog.open;
    dialog.addEventListener('close', () => { dialog.hidden = true; triggers.get(dialog)?.focus(); });
    dialog.addEventListener('click', event => { if (event.target === dialog) close(dialog); });
  });
  document.addEventListener('click', event => {
    const link = event.target.closest?.('[data-bse-document]');
    if (!link || !link.closest('.bse-root')) return;
    const dialog = document.getElementById(`bse-document-${link.dataset.bseDocument}`);
    if (!dialog || !native?.showModal) return;
    try {
      triggers.set(dialog, link); dialog.hidden = false;
      if (!dialog.open) native.showModal.call(dialog);
      if (!dialog.open) throw new Error('unavailable');
      event.preventDefault();
    } catch (_) { dialog.hidden = !dialog.open; /* Follow the existing document URL. */ }
  }, true);
})();
