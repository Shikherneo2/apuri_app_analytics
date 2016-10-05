function setActive() {
    aObj = document.getElementById('nav').getElementsByTagName('a');
    aObj1 = document.getElementById('nav').getElementsByTagName('li');
    for(i=0;i<aObj1.length;i++) {
        if(document.location.href.indexOf(aObj[i].href)>=0) {
            aObj1[i].className='active';
        }
    }
}

window.onload = setActive;