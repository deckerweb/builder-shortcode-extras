/* Native modal: keyboard containment, Escape, focus return and progressive fallback. */
(() => {
 'use strict';
 const dialog = document.getElementById('dwl-history');
 const trigger = document.querySelector('[data-dwl-history]');
 if (!dialog || !trigger) return;
 let previous;
 trigger.addEventListener('click', () => {
  previous = document.activeElement;
  if (typeof dialog.showModal === 'function') dialog.showModal();
  else { dialog.setAttribute('open', ''); dialog.querySelector('[data-dwl-close]').focus(); }
 });
 const close = () => {
  if (typeof dialog.close === 'function') dialog.close();
  else { dialog.removeAttribute('open'); if (previous) previous.focus(); }
 };
 dialog.querySelector('[data-dwl-close]').addEventListener('click', close);
 dialog.addEventListener('close', () => { if (previous) previous.focus(); });
 dialog.addEventListener('keydown', (event) => {
  if (event.key === 'Escape') { event.preventDefault(); close(); }
  if (event.key === 'Tab') {
   const items = [...dialog.querySelectorAll('button, a[href], input, select, textarea, [tabindex="0"]')].filter(item => !item.disabled);
   const first = items[0], last = items[items.length - 1];
   if (event.shiftKey && document.activeElement === first) { event.preventDefault(); last.focus(); }
   else if (!event.shiftKey && document.activeElement === last) { event.preventDefault(); first.focus(); }
  }
 });
})();
/* Catalog actions keep the current page, filters and scroll position intact. */
(($) => {
 'use strict';
 $(function () {
  if (!window.dwlInline) return;
  let busy = false, pending = null;
  const credentials = () => {
   const c = window.wp?.updates?.filesystemCredentials;
   return c ? {hostname:c.ftp.hostname,username:c.ftp.username,password:c.ftp.password,connection_type:c.ftp.connectionType,public_key:c.ssh.publicKey,private_key:c.ssh.privateKey,_fs_nonce:c.fsNonce} : {};
  };
  const finish = (job, message, failed) => {
   busy = false; pending = null;
   job.button.disabled = false; job.button.textContent = job.label;
   job.form.removeAttribute('aria-busy');
   job.status.textContent = message;
   job.status.classList.toggle('dwl-inline-error', failed);
   if (window.wp?.a11y) wp.a11y.speak(message, failed ? 'assertive' : 'polite');
  };
  const apply = (job, data) => {
   delete job.form.dataset.dwlRecover;delete job.form.dataset.dwlLabel;
   finish(job, data.message || '', false);
   if(data.version){const version=job.form.closest('.dwl-card')?.querySelector('footer span:last-child');if(version)version.textContent='v'+data.version;}
   if (data.next) {
    job.form.dataset.dwlAction = 'activate';job.form.querySelector('[name="_wpnonce"]').value = data.next.nonce;
    const url = new URL(job.form.action);url.searchParams.set('action','dwl_activate');job.form.action=url.href;
    job.button.textContent = data.next.label;job.button.classList.add('button-primary');
   } else if (data.updated || data.active || data.installed) {job.button.textContent=data.message;job.button.disabled=true;}
  };
  const checkStatus = (job) => {
   job.button.textContent=dwlInline.checking;job.form.setAttribute('aria-busy','true');
   const unavailable = (message) => {
    finish(job,message || dwlInline.failed,true);
    job.form.dataset.dwlRecover='1';job.form.dataset.dwlLabel=job.label;job.button.textContent=dwlInline.status;
   };
   $.ajax({url:dwlInline.url,method:'POST',dataType:'json',data:{action:'dwl_inline_status',operation:job.form.dataset.dwlAction,slug:job.form.dataset.dwlSlug,network:job.form.dataset.dwlNetwork,_wpnonce:job.form.querySelector('[name="_wpnonce"]').value}}).done(response=>{
    const data=response?.data || {};
    if (!response.success || data.pending) {unavailable(data.message);return;}
    apply(job,data);
   }).fail(()=>unavailable(dwlInline.failed));
  };
  const send = (job) => {
   $.ajax({url:dwlInline.url,method:'POST',dataType:'json',data:{...credentials(),action:'dwl_inline',operation:job.form.dataset.dwlAction,slug:job.form.dataset.dwlSlug,network:job.form.dataset.dwlNetwork,_wpnonce:job.form.querySelector('[name="_wpnonce"]').value}}).done(response => {
    const data = response?.data || {};
    if (!response.success) {
     if (data.credentials && document.getElementById('request-filesystem-credentials-dialog') && window.wp?.updates) {
      pending = job;
      wp.updates.filesystemCredentials.available = false;
      wp.updates.ajaxLocked = true;
      wp.updates.$elToReturnFocusToFromCredentialsModal = $(job.button);
      wp.updates.requestForCredentialsModalOpen();
      return;
     }
     finish(job, data.message || dwlInline.failed, true); return;
    }
    apply(job,data);
   }).fail(() => checkStatus(job));
  };
  $('#deckerweb-library').on('submit', '.dwl-card form[data-dwl-action]', function(event) {
   if (!['install','activate','update'].includes(this.dataset.dwlAction)) return;
   event.preventDefault(); if (busy) return;
   busy = true;
   const button = this.querySelector('button[type="submit"]');
   let status = this.parentElement.querySelector('.dwl-inline-status');
   if (!status) {status=document.createElement('p');status.className='dwl-inline-status';status.setAttribute('role','status');status.setAttribute('aria-live','polite');this.after(status);}
   const job={form:this,button,status,label:this.dataset.dwlLabel || button.textContent};
   status.textContent='';status.classList.remove('dwl-inline-error');button.disabled=true;
   button.textContent=this.dataset.dwlAction==='install'?dwlInline.installing:(this.dataset.dwlAction==='update'?dwlInline.updating:dwlInline.activating);
   this.setAttribute('aria-busy','true');if(this.dataset.dwlRecover==='1')checkStatus(job);else send(job);
  });
  // WordPress's modal handler records credentials in memory before this retry.
  $('#request-filesystem-credentials-dialog').on('submit.dwl', 'form', () => {
   if (pending) {const job=pending;pending=null;send(job);}
  });
  const cancel = () => {if(pending) {const job=pending;finish(job,dwlInline.cancelled,false);job.button.focus();}};
  $('#request-filesystem-credentials-dialog').on('click.dwl','.cancel-button',cancel).on('keydown.dwl',event=>{if(event.key==='Escape')cancel();});
 });
})(jQuery);

/* Shared preferences and catalog controls have a native same-admin fallback. */
(($) => {
 'use strict';
 $(document).on('submit', 'form[data-dwl-action]', function(event) {
  const operation=this.dataset.dwlAction;
  if (!window.dwlInline || !['preferences','dismiss','refresh'].includes(operation)) return;
  event.preventDefault();
  const form=this, button=form.querySelector('button[type="submit"],input[type="submit"]');
  if(form.dataset.dwlBusy==='1')return;form.dataset.dwlBusy='1';if(button)button.disabled=true;
  let status=form.querySelector('.dwl-control-status');
  if(!status){status=document.createElement('p');status.className='dwl-control-status';status.setAttribute('role','status');form.append(status);}
  const data=$(form).serializeArray();data.push({name:'action',value:'dwl_controls'},{name:'operation',value:operation},{name:'network',value:form.dataset.dwlNetwork});
  $.ajax({url:dwlInline.url,method:'POST',dataType:'json',data}).done(response=>{
   status.textContent=response.data?.message || dwlInline.failed;
   if(response.success && operation==='dismiss'){form.closest('.notice')?.remove();}
   if(response.success && operation==='preferences' && form.querySelector('[name="hide_only"]')){document.querySelectorAll('#deckerweb-library .dwl-grid,#deckerweb-library .dwl-filter').forEach(el=>{el.hidden=true;});}
   if(response.success && operation==='refresh'){window.location.reload();}
  }).fail(()=>{status.textContent=dwlInline.failed;}).always(()=>{delete form.dataset.dwlBusy;if(button)button.disabled=false;});
 });
})(jQuery);
