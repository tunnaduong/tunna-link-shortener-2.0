// Get the button element
const button = $("#next_btn");

var s = 6;
var timer = setInterval(() => {
  s--;
  button.text("Vui lòng đợi " + s + " giây...");
  if (s == -1) {
    clearInterval(timer);
    $("#next_btn").removeClass("disabled-button");
  }
}, 1200);

// // Set the initial button text
// button.text("Vui lòng đợi " + s + " giây...");

// Set a 6-second timer
setTimeout(() => {
  // Change the button text after 6 seconds
  button.text("Bấm vào đây để tiếp tục!");
}, 8400);

// Get the modal element
var modal = document.getElementById("myModal");

// Get the button that opens the modal
var btn = document.getElementById("openModalBtn");

// Get the close button element
var closeBtn = document.getElementsByClassName("close")[0];

// When the user clicks the button, open the modal
btn.onclick = function () {
  modal.style.display = "block";
};

// When the user clicks on the close button, close the modal
closeBtn.onclick = function () {
  modal.style.display = "none";
};

// When the user clicks outside the modal, close it
window.onclick = function (event) {
  if (event.target == modal) {
    modal.style.display = "none";
  }
};
