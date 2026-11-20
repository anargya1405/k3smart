(() => {
'use strict';
const ready = () => {
 const motion = matchMedia('(prefers-reduced-motion: reduce)');
 const fine = matchMedia('(any-hover: hover) and (any-pointer: fine)');
 const popovers = [...document.querySelectorAll('[data-popover], .notifications')];
 const timers = new WeakMap();
 const firstSummary = el => el.querySelector(':scope > summary');
 const header=document.querySelector('.navigation-header');
 const alignPanels=()=>{if(header)document.documentElement.style.setProperty('--nav-panel-top',(Math.ceil(header.getBoundingClientRect().bottom)+8)+'px');};
 alignPanels();
 if(header && 'ResizeObserver' in window)new ResizeObserver(alignPanels).observe(header);
 window.addEventListener('resize',alignPanels,{passive:true});

 const close = (el, restore = false) => { clearTimeout(timers.get(el)); el.open = false; firstSummary(el)?.setAttribute('aria-expanded','false'); if(restore) firstSummary(el)?.focus(); };
 const closeOthers = keep => popovers.forEach(el => { if(el !== keep) close(el); });
 const nav = document.querySelector('.desktop-navigation');
 const indicator = nav?.querySelector('.nav-indicator');
 const triggers = nav ? [...nav.querySelectorAll('.nav-trigger')] : [];
 let indicatorTarget = null;
 const positionIndicator = target => {
  if(!nav || !indicator) return;
  indicatorTarget=target;
  if(!target){indicator.style.opacity='0';return;}
  const rect=target.getBoundingClientRect(), parent=nav.getBoundingClientRect();
  indicator.style.width=rect.width+'px';indicator.style.translate=(rect.left-parent.left-1)+'px 0';indicator.style.opacity='1';nav.classList.add('has-indicator');
 };
 const selected = () => nav?.querySelector('details[open] > summary') || nav?.querySelector('.nav-trigger.current');
 popovers.forEach(el => {
  const summary=firstSummary(el);
  summary?.setAttribute('aria-expanded',String(el.open));
  el.addEventListener('toggle',()=>{
   summary?.setAttribute('aria-expanded',String(el.open));
   if(el.open)closeOthers(el);
   positionIndicator(selected());
  });
  el.addEventListener('keydown',e=>{
   if(e.key==='Escape' && el.open){e.preventDefault();e.stopPropagation();close(el,true);positionIndicator(selected());}
   if(e.key==='ArrowDown' && e.target===summary){e.preventDefault();el.open=true;closeOthers(el);el.querySelector('a,button')?.focus();}
   if(e.key==='Home' && el.open && e.target!==summary){const link=el.querySelector('a,button');if(link){e.preventDefault();link.focus();}}
  });
  el.addEventListener('focusout',()=>setTimeout(()=>{
   if(el.open && !el.contains(document.activeElement) && !el.matches(':hover'))close(el);
  },0));
  if(el.classList.contains('mega-menu')){
   el.addEventListener('pointerenter',e=>{
    if(!fine.matches || e.pointerType!=='mouse')return;
    clearTimeout(timers.get(el));
    timers.set(el,setTimeout(()=>{closeOthers(el);el.open=true;positionIndicator(summary);},130));
   });
   el.addEventListener('pointerleave',()=>{
    clearTimeout(timers.get(el));
    timers.set(el,setTimeout(()=>{if(!el.contains(document.activeElement)){close(el);positionIndicator(selected());}},180));
   });
  }
 });
 triggers.forEach((t,i)=>{
  t.addEventListener('pointerenter',()=>positionIndicator(t));
  t.addEventListener('focus',()=>positionIndicator(t));
  t.addEventListener('keydown',e=>{
   if(['ArrowRight','ArrowLeft'].includes(e.key)){
    e.preventDefault();triggers[(i+(e.key==='ArrowRight'?1:triggers.length-1))%triggers.length].focus();
   }
  });
 });
 nav?.addEventListener('pointerleave',()=>positionIndicator(selected()));
 document.addEventListener('pointerdown',e=>popovers.forEach(el=>{if(!el.contains(e.target))close(el);}));
 document.addEventListener('keydown',e=>{if(e.key==='Escape')popovers.forEach(el=>{if(el.open)close(el,true);});});
 document.addEventListener('click',e=>{if(e.target.closest('.mega-link,.create-grid a,.fab-list a'))closeOthers(null);});
 positionIndicator(selected());
 window.addEventListener('resize',()=>positionIndicator(indicatorTarget?.isConnected?indicatorTarget:selected()),{passive:true});
 // Native cursor stays visible; the ring follows the mouse and snaps to interactive targets.
 let ring=document.querySelector('.magnetic-ring');
 if(!ring){ring=document.createElement('div');ring.className='magnetic-ring';ring.setAttribute('aria-hidden','true');document.body.append(ring);}
 const selector='button:not(:disabled),a.button,.pill-button,.header-login,.auth-submit,.auth-tabs a,.auth-top a,.site-header a,.nav-trigger,.mega-link,.create-grid a,.fab-list a,.text-link,summary,.stock-directions label';
 let active=null,frame=0,base=null,current=null,goal={x:0,y:0};
 const release=()=>{if(active){active.style.removeProperty('--magnetic-x');active.style.removeProperty('--magnetic-y');}active=null;base=null;};
 const reset=()=>{release();ring?.classList.remove('visible','snapped');cancelAnimationFrame(frame);frame=0;current=null;};
 const tick=()=>{
  frame=0;if(!ring||!current)return;
  current.x+=(goal.x-current.x)*.3;current.y+=(goal.y-current.y)*.3;
  ring.style.transform='translate3d('+current.x+'px,'+current.y+'px,0)';
  if(Math.abs(goal.x-current.x)+Math.abs(goal.y-current.y)>.1)frame=requestAnimationFrame(tick);
 };
 document.addEventListener('pointermove',e=>{
  if(!ring||motion.matches||!fine.matches||e.pointerType!=='mouse'){reset();return;}
  if(e.target.closest('input,textarea,select,[contenteditable="true"]')){reset();return;}
  const target=e.target.closest(selector);
  if(target!==active){release();active=target;if(active)base=active.getBoundingClientRect();}
  let width=20,height=20,radius=50;
  if(active&&base){
   width=base.width+8;height=base.height+8;
   radius=(parseFloat(getComputedStyle(active).borderRadius)||8)+4;
   const x=Math.max(-4,Math.min(4,(e.clientX-base.left-base.width/2)*.10));
   const y=Math.max(-3,Math.min(3,(e.clientY-base.top-base.height/2)*.10));
   // Translate primary actions only; rows and navigation remain stable.
   if(active.matches('[data-magnetic],button,a.button')){
    active.setAttribute('data-magnetic','');
    active.style.setProperty('--magnetic-x',x+'px');active.style.setProperty('--magnetic-y',y+'px');
   }
   goal={x:base.left-4,y:base.top-4};
  }else goal={x:e.clientX-10,y:e.clientY-10};
  ring.style.width=width+'px';ring.style.height=height+'px';ring.style.borderRadius=radius+'px';
  ring.classList.toggle('snapped',!!active);ring.classList.add('visible');
  if(!current){current={...goal};ring.style.transform='translate3d('+current.x+'px,'+current.y+'px,0)';}if(!frame)frame=requestAnimationFrame(tick);
 },{passive:true});
 document.addEventListener('pointerout',e=>{if(!e.relatedTarget)reset();});
 document.addEventListener('pointerdown',reset);
 document.addEventListener('keydown',reset);
 window.addEventListener('blur',reset);
 window.addEventListener('scroll',reset,{passive:true});
 window.addEventListener('resize',reset,{passive:true});
 motion.addEventListener('change',reset);fine.addEventListener('change',reset);
 // Dismissible feedback, inspired by the Base UI toast pattern. Errors remain visible.
 document.querySelectorAll('.notice:not(.error)').forEach(notice=>{
  const button=document.createElement('button');
  button.type='button';button.className='notice-dismiss';button.setAttribute('aria-label','Tutup pemberitahuan');
  button.innerHTML='<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m6 6 12 12M6 18 18 6"/></svg>';
  button.addEventListener('click',()=>notice.remove());notice.append(button);
 });
};
if(document.readyState==='loading')document.addEventListener('DOMContentLoaded',ready);else ready();
})();