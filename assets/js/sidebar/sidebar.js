let sidebarIsOpened = false;

$('.sidebar').mouseover((e) => {
    e.stopPropagation()
    e.preventDefault()
    document.body.classList.add('has-sidebar');
    sidebarIsOpened = true;

})

$('body').click(() => {
    if(sidebarIsOpened)
    {
        document.body.classList.remove('has-sidebar');
    }
})

$('.menu').mouseleave((e) => {
    if(sidebarIsOpened)
    {
        document.body.classList.remove('has-sidebar');
    }
})