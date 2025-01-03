let filterTags = document.getElementById('filterTagHolder').getElementsByTagName('input');
let setterEl = document.getElementById('ajaxSetter');
let querySearchEl = document.getElementById('querySearch');
var offsetEl = document.getElementById('pageNumber');

oldHref = setterEl.href;

function search(el){
    setterEl.href = setterEl.href.replace('queryValue',querySearchEl.value);
    setterEl.href = setterEl.href.replace('tagRawValue',JSON.stringify(buildTagRawValue()));

    setterEl.href = setterEl.href.replace('offsetValue',offsetEl.value-1);

    document.getElementById('ajaxSetter').click()
    setterEl.href = oldHref;
}

function changeOffset(offset){
    old = offsetEl.value;
    if(offset <= 0 || offset > maxPage) {
        offsetEl.value = Math.min(Math.max(maxPage,1), Math.max(1, offset));
        if(old == offsetEl.value){
            return;
        }
    }else{
        offsetEl.value = offset;
    }
    window.scrollTo(0, 0);
    search();
}

function buildTagRawValue(){
    let tagRawValue = [];
    for (const tag of filterTags) {
        if(tag.checked) {
            tagRawValue.push(tag.id.replace('tag-',''));
        }
    }
    return tagRawValue;
}