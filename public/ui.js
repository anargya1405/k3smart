document.addEventListener('DOMContentLoaded',()=>{const menu=document.querySelector('[data-menu-toggle]');const close=()=>{document.body.classList.remove('menu-open');document.getElementById('sidebar')?.classList.remove('open');menu?.setAttribute('aria-expanded','false');};menu?.addEventListener('click',()=>{const open=!document.body.classList.contains('menu-open');document.body.classList.toggle('menu-open',open);document.getElementById('sidebar')?.classList.toggle('open',open);menu.setAttribute('aria-expanded',String(open));});document.querySelector('[data-menu-close]')?.addEventListener('click',close);document.addEventListener('keydown',e=>{if(e.key==='Escape'){close();document.querySelectorAll('.notifications[open]').forEach(d=>d.open=false);}});document.querySelector('[data-password-toggle]')?.addEventListener('click',e=>{const p=document.getElementById('password');const show=p.type==='password';p.type=show?'text':'password';e.currentTarget.setAttribute('aria-pressed',String(show));e.currentTarget.setAttribute('aria-label',show?'Sembunyikan kata sandi':'Tampilkan kata sandi');});document.addEventListener('click',e=>{document.querySelectorAll('.notifications[open]').forEach(d=>{if(!d.contains(e.target))d.open=false;});});});
/* Konfirmasi hapus: dialog kustom; tanpa dukungan <dialog> memakai confirm() bawaan. */
document.addEventListener('DOMContentLoaded',()=>{
 const dialog=document.getElementById('confirm-dialog');
 let pending=null,opener=null;
 document.addEventListener('submit',e=>{
  const form=e.target.closest?.('form[data-confirm]');
  if(!form||form.dataset.confirmed==='1')return;
  e.preventDefault();
  if(!dialog||typeof dialog.showModal!=='function'){if(confirm(form.dataset.confirm)){form.dataset.confirmed='1';form.submit();}return;}
  pending=form;opener=document.activeElement;
  dialog.querySelector('#confirm-title').textContent=form.dataset.confirmTitle||'Hapus data?';
  dialog.querySelector('#confirm-message').textContent=form.dataset.confirm;
  dialog.showModal();dialog.querySelector('[data-confirm-cancel]').focus();
 },true);
 if(!dialog)return;
 const close=()=>{dialog.close();pending=null;opener?.focus?.();};
 dialog.querySelector('[data-confirm-cancel]').addEventListener('click',close);
 dialog.addEventListener('click',e=>{if(e.target===dialog)close();});
 dialog.addEventListener('cancel',e=>{e.preventDefault();close();});
 dialog.querySelector('[data-confirm-ok]').addEventListener('click',e=>{
  if(!pending)return;const form=pending;e.currentTarget.disabled=true;e.currentTarget.textContent='Menghapus…';form.dataset.confirmed='1';form.submit();
 });
});
