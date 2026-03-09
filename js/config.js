function updateList(selected){
    document.querySelectorAll('.selectable-item').forEach(item => {
        item.classList.remove('selected');
    });
    
    selected.closest('.selectable-item').classList.add('selected');

    const itemid = selected.value;
    document.getElementById('item-id-env').value = itemid;
}
