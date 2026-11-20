(() => {
'use strict';
const init=()=>{
 const reduce=matchMedia('(prefers-reduced-motion: reduce)');
 const root=document.documentElement;
 const hero=document.querySelector('.zoom-hero'),stage=document.querySelector('.hero-stage');
 const header=document.querySelector('.site-header'),progress=document.querySelector('.reading-progress');
 const passage=document.querySelector('[data-highlight]');
 const panels=[...document.querySelectorAll('[data-parallax]')];
 const stories=[...document.querySelectorAll('[data-scroll-feature]')];
 let words=[],frame=0;
 const clamp=(v,min=0,max=1)=>Math.max(min,Math.min(max,v));
 const lerp=(a,b,t)=>a+(b-a)*t;

 root.classList.add('js-motion');
 const ready=()=>root.classList.add('page-ready');
 requestAnimationFrame(ready);setTimeout(ready,350); // cadangan bila tab dibuka di latar belakang

 if(passage){
  const text=passage.textContent.trim();
  passage.setAttribute('aria-label',text);
  const fragment=document.createDocumentFragment();
  text.split(/\s+/).forEach(word=>{const span=document.createElement('span');span.className='highlight-word';span.textContent=word;span.setAttribute('aria-hidden','true');fragment.append(span,document.createTextNode(' '));words.push(span);});
  passage.replaceChildren(fragment);
 }

 /* Reveal on scroll: elements fade/slide in once, with a small stagger per group. */
 const groups=['.section-topline','.manifesto-label','.intro-description','.scope-strip>*','.features-intro h2','.features-intro>p','.feature-photo','.feature-story','.workflow-title>*','.workflow-grid article','.closing-section h2','.closing-bottom>*','.photo-copy>*','.footer-main>*'];
 const revealItems=[];
 groups.forEach(sel=>document.querySelectorAll(sel).forEach((el,i)=>{el.classList.add('reveal');el.style.setProperty('--reveal-delay',(i%4)*90+'ms');revealItems.push(el);}));
 const counters=[...document.querySelectorAll('.scope-strip strong')].filter(s=>/^\d+/.test(s.firstChild?.textContent?.trim()||''));
 const countUp=el=>{
  const node=el.firstChild,target=parseInt(node.textContent,10),pad=node.textContent.trim().length;
  if(reduce.matches){return;}
  const start=performance.now(),dur=1100;
  const step=t=>{const p=clamp((t-start)/dur),e=1-Math.pow(1-p,3);node.textContent=String(Math.round(target*e)).padStart(pad,'0');if(p<1)requestAnimationFrame(step);};
  node.textContent='0'.padStart(pad,'0');requestAnimationFrame(step);
 };
 if('IntersectionObserver' in window&&!reduce.matches){
  const io=new IntersectionObserver(entries=>entries.forEach(entry=>{
   if(!entry.isIntersecting)return;
   entry.target.classList.add('in');
   if(counters.includes(entry.target.querySelector?.('strong'))||counters.includes(entry.target))countUp(entry.target.matches('strong')?entry.target:entry.target.querySelector('strong'));
   io.unobserve(entry.target);
  }),{rootMargin:'0px 0px -8% 0px',threshold:.12});
  revealItems.forEach(el=>io.observe(el));
 }else revealItems.forEach(el=>el.classList.add('in'));

 /* Scroll-linked effects use eased values so movement glides instead of snapping. */
 const state={scale:1,blur:0,img:1,ty:0,to:1,progress:0,lit:0,parallax:new Map()};
 const render=()=>{
  frame=0;
  const height=innerHeight,y=scrollY,total=root.scrollHeight-height;
  const ease=reduce.matches?1:.14;
  let moving=false;
  const approach=(key,target,precision=.001)=>{const next=lerp(state[key],target,ease);state[key]=Math.abs(next-target)<precision?target:next;if(state[key]!==target)moving=true;return state[key];};
  header?.classList.toggle('scrolled',y>40);
  root.classList.toggle('scroll-enhanced',!reduce.matches);
  if(progress)progress.style.transform='scaleX('+approach('progress',clamp(total>0?y/total:0))+')';
  if(hero&&stage){
   const rect=hero.getBoundingClientRect(),range=Math.max(1,hero.offsetHeight-stage.offsetHeight);
   const p=reduce.matches?0:clamp(-rect.top/range);
   hero.style.setProperty('--hero-scale',approach('scale',1+p*.14).toFixed(4));
   hero.style.setProperty('--hero-blur',approach('blur',p*p*1.6,.01).toFixed(2)+'px');
   hero.style.setProperty('--hero-image-opacity',approach('img',1-p*.25).toFixed(3));
   hero.style.setProperty('--hero-text-y',approach('ty',-p*90,.1).toFixed(1)+'px');
   hero.style.setProperty('--hero-text-opacity',approach('to',1-clamp(p*1.6)).toFixed(3));
  }
  if(passage){
   const rect=passage.getBoundingClientRect();
   const p=reduce.matches?1:clamp((height*.75-rect.top)/(rect.height+height*.35));
   const lit=approach('lit',p);
   words.forEach((word,i)=>word.classList.toggle('lit',i/Math.max(1,words.length-1)<=lit));
  }
  panels.forEach(panel=>{
   const section=panel.closest('.parallax-section'),rect=section.getBoundingClientRect();
   if(rect.bottom<-200||rect.top>height+200)return;
   const amount=Number(panel.dataset.parallax)||.1;
   const target=reduce.matches?0:clamp((height/2-(rect.top+rect.height/2))*amount,-rect.height*.1,rect.height*.1);
   const cur=state.parallax.get(panel)??target;
   let next=lerp(cur,target,ease);if(Math.abs(next-target)<.1)next=target;else moving=true;
   state.parallax.set(panel,next);
   panel.style.setProperty('--parallax-y',next.toFixed(1)+'px');
  });
  let chosen=null,distance=Infinity;
  stories.forEach(story=>{
   const r=story.getBoundingClientRect();
   const d=Math.abs(r.top+Math.min(r.height/2,160)-height*.48);
   if(d<distance){distance=d;chosen=story;}
  });
  stories.forEach(story=>story.classList.toggle('active',story===chosen));
  if(moving)schedule();
 };
 const schedule=()=>{if(!frame)frame=requestAnimationFrame(render);};
 window.addEventListener('scroll',schedule,{passive:true});
 window.addEventListener('resize',schedule,{passive:true});
 window.addEventListener('load',schedule,{once:true});
 reduce.addEventListener('change',schedule);
 document.querySelectorAll('.feature-story details').forEach(d=>d.addEventListener('toggle',schedule));
 document.fonts?.ready.then(schedule);

 /* Navbar: sliding indicator for the active section, hide on scroll down, show on scroll up. */
 const navLinks=[...document.querySelectorAll('.desktop-links a[href^="#"]')];
 const indicator=document.querySelector('.nav-indicator');
 const sections=navLinks.map(a=>document.querySelector(a.getAttribute('href'))).filter(Boolean);
 let activeLink=null,hoverLink=null,lastY=scrollY;
 const placeIndicator=()=>{
  const target=hoverLink||activeLink;
  if(!indicator)return;
  if(!target){indicator.classList.remove('on');return;}
  indicator.style.setProperty('--nav-x',target.offsetLeft+'px');
  indicator.style.setProperty('--nav-w',target.offsetWidth+'px');
  indicator.classList.add('on');
  navLinks.forEach(a=>a.classList.toggle('active',a===target));
 };
 const updateActive=()=>{
  const line=innerHeight*.35;let current=null;
  sections.forEach((s,i)=>{const r=s.getBoundingClientRect();if(r.top<=line&&r.bottom>line)current=navLinks[i];});
  if(current!==activeLink){activeLink=current;if(current)current.setAttribute('aria-current','true');navLinks.forEach(a=>{if(a!==current)a.removeAttribute('aria-current');});placeIndicator();}
 };
 navLinks.forEach(a=>{
  a.addEventListener('pointerenter',()=>{hoverLink=a;placeIndicator();});
  a.addEventListener('focus',()=>{hoverLink=a;placeIndicator();});
  a.addEventListener('blur',()=>{hoverLink=null;placeIndicator();});
 });
 document.querySelector('.desktop-links')?.addEventListener('pointerleave',()=>{hoverLink=null;placeIndicator();});
 const navScroll=()=>{
  const y=scrollY,menuOpen=document.querySelector('.mobile-navigation')?.open;
  if(header&&!menuOpen&&!header.contains(document.activeElement)){
   if(y>lastY+6&&y>innerHeight*.6)header.classList.add('nav-hidden');
   else if(y<lastY-6||y<innerHeight*.6)header.classList.remove('nav-hidden');
  }
  lastY=y;updateActive();
 };
 window.addEventListener('scroll',navScroll,{passive:true});
 window.addEventListener('resize',placeIndicator,{passive:true});
 header?.addEventListener('focusin',()=>header.classList.remove('nav-hidden'));
 document.fonts?.ready.then(placeIndicator);
 updateActive();

 /* Gentle pointer tilt on the hero illustration (desktop pointers only). */
 const visual=document.querySelector('.hero-visual');
 if(visual&&matchMedia('(pointer:fine)').matches){
  let tx=0,ty=0,cx=0,cy=0,raf=0;
  const loop=()=>{cx=lerp(cx,tx,.08);cy=lerp(cy,ty,.08);visual.style.setProperty('--tilt-x',cx.toFixed(2)+'px');visual.style.setProperty('--tilt-y',cy.toFixed(2)+'px');raf=(Math.abs(cx-tx)+Math.abs(cy-ty)>.05)?requestAnimationFrame(loop):0;};
  stage.addEventListener('pointermove',e=>{if(reduce.matches)return;tx=(e.clientX/innerWidth-.5)*-18;ty=(e.clientY/innerHeight-.5)*-12;if(!raf)raf=requestAnimationFrame(loop);});
  stage.addEventListener('pointerleave',()=>{tx=0;ty=0;if(!raf)raf=requestAnimationFrame(loop);});
 }

 const menu=document.querySelector('.mobile-navigation');
 if(menu){
  const summary=menu.querySelector('summary');
  const close=restore=>{menu.open=false;summary.setAttribute('aria-expanded','false');if(restore)summary.focus();};
  summary.setAttribute('aria-expanded','false');
  menu.addEventListener('toggle',()=>summary.setAttribute('aria-expanded',String(menu.open)));
  menu.addEventListener('keydown',e=>{if(e.key==='Escape'&&menu.open){close(true);e.preventDefault();}});
  menu.querySelectorAll('a').forEach(a=>a.addEventListener('click',()=>close(false)));
  document.addEventListener('pointerdown',e=>{if(!menu.contains(e.target))close(false);});
 }
 render();
};
if(document.readyState==='loading')document.addEventListener('DOMContentLoaded',init);else init();
})();
