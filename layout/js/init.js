//to activate the dropdown menu in nav bar
$( document ).ready(function(){
    $('.dropdown-trigger').dropdown({
        constrainWidth: false, // Does not change width of dropdown to that of the activator
        hover: true, // Activate on hover
        alignment: 'left', // Aligns dropdown to left or right edge (works with constrain_width)
        belowOrigin: true // Displays dropdown below the button
    });
});
