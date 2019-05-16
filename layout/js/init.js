document.addEventListener('DOMContentLoaded', function() {
    var elemsDropdown = document.querySelectorAll('.dropdown-trigger');
    var instancesDropdown = M.Dropdown.init(elemsDropdown);
    var elemsSidenav = document.querySelectorAll('.sidenav');
    var instancesSidenav = M.Sidenav.init(elemsSidenav);
});