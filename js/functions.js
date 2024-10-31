var pttax_debug_toggler = document.getElementsByClassName("pttax_debug_caret");
var i;

for (i = 0; i < pttax_debug_toggler.length; i++) {
    pttax_debug_toggler[i].addEventListener("click", function() {
    this.parentElement.querySelector(".pttax_debug_nested").classList.toggle("pttax_debug_active");
    this.classList.toggle("pttax_debug_caret-down");
  });
}
