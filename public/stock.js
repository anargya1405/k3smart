document.querySelectorAll('[data-stock-form]').forEach(form=>{
 const quantity=form.elements.quantity, preview=form.querySelector('[data-stock-preview]');
 const stock=Number(form.dataset.stock), unit=form.dataset.unit;
 const update=()=>{
  const outgoing=form.elements.direction.value==='out';
  quantity.max=String(outgoing?Math.min(stock,10000):10000);
  const amount=Number(quantity.value), valid=quantity.value!=='' && Number.isInteger(amount) && amount>0;
  const next=stock+(outgoing?-amount:amount);
  preview.classList.toggle('stock-warning',valid&&outgoing&&next<0);
  preview.textContent=!valid?'Stok saat ini: '+stock+' '+unit
   :outgoing&&next<0?'Stok tidak cukup. Maksimal '+stock+' '+unit+'.'
   :'Stok setelah mutasi: '+stock+' '+(outgoing?'−':'+')+' '+amount+' = '+next+' '+unit;
 };
 form.addEventListener('input',update);form.addEventListener('change',update);update();
});