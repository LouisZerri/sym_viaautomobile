$(document).ready(function() {

    $('.sidebar').click((e) => {
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

})