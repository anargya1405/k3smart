(() => {
'use strict';
document.addEventListener('DOMContentLoaded',()=>{
 if(!document.querySelector('.magnetic-ring')){const ring=document.createElement('div');ring.className='magnetic-ring';ring.setAttribute('aria-hidden','true');document.body.append(ring);}
});
})();
