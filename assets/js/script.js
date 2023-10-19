// When the DOM is ready
$(document).ready(function () {
  var modal = $("#myModal");
  var btn = $("#openModalBtn");
  var closeBtn = $(".close");

  // When the user clicks the button, open the modal
  btn.click(function () {
    modal.css("display", "block");
  });

  // When the user clicks on the close button, close the modal
  closeBtn.click(function () {
    modal.css("display", "none");
  });

  // When the user clicks outside the modal, close it
  $(window).click(function (event) {
    if (event.target == modal[0]) {
      modal.css("display", "none");
    }
  });
});

// Get all links with a class of "scroll-link"
const scrollLinks = document.querySelectorAll(".scroll-link");

// Add a click event listener to each scroll link
scrollLinks.forEach((link) => {
  link.addEventListener("click", (event) => {
    // Prevent the default link behavior
    event.preventDefault();

    // Get the ID of the target element from the link's href attribute
    const targetId = link.getAttribute("href").substring(1);

    // Get the target element by ID
    const targetElement = document.getElementById(targetId);

    // Scroll to the target element using the scrollIntoView method
    targetElement.scrollIntoView({
      behavior: "smooth",
    });
  });
});

// JavaScript to open Facebook share dialog in a popup window
function fbShare() {
  var shareUrl =
    "https://www.facebook.com/sharer/sharer.php?u=" +
    location.href +
    "&hashtag=%23TunnaDuongLinkShortener";
  window.open(shareUrl, "Facebook Share", "width=600,height=400");
}
