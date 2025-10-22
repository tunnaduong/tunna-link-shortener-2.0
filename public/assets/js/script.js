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
});

// JavaScript to open Facebook share dialog in a popup window
function fbShare() {
  var shareUrl =
    "https://www.facebook.com/sharer/sharer.php?u=" +
    location.href +
    "&hashtag=%23TunnaDuongLinkShortener";
  window.open(shareUrl, "Facebook Share", "width=600,height=400");
}

function openNewWindow(url) {
  // Track redirect completion when user actually clicks to continue
  // Wait for tracking to complete before proceeding
  handleTrackDidContinue().then(() => {
    // Check if it's a javascript: URL
    if (url.startsWith("javascript:")) {
      // Extract the JavaScript code
      const jsCode = url.substring(11); // Remove "javascript:" prefix

      // Create a new window and execute the JavaScript
      const newWindow = window.open("", "_blank");
      if (newWindow) {
        newWindow.document.write(`
          <!DOCTYPE html>
          <html>
          <head>
            <title>JavaScript Execution</title>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
          </head>
          <body>
            <script>
              try {
                ${jsCode.replace(/'/g, "'")}
              } catch (e) {
                alert('Error executing JavaScript: ' + e.message);
              }
            </script>
          </body>
          </html>
        `);
        newWindow.document.close();
      }
    } else {
      // For regular URLs, use the normal window.open
      window.open(url, "_blank");
    }

    setTimeout(function () {
      window.location.href = "https://shope.ee/7zlMOzSB7w";
    }, 1000);
  });
}

// Function to track redirect completion (simplified: only use fetch POST)
function handleTrackDidContinue() {
  return new Promise((resolve, reject) => {
    try {
      // Get tracker ID from global variable set by PHP
      const trackerId = window.linkData?.trackerId;
      const linkCode =
        window.linkData?.code || window.location.pathname.substring(1);

      if (trackerId) {
        console.log("Tracking redirect completion for tracker ID:", trackerId);

        fetch("/api/tracker/complete", {
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
          },
          body: "tracker_id=" + encodeURIComponent(trackerId),
        })
          .then((response) => response.json())
          .then((data) => {
            console.log("Redirect completion tracked via fetch:", data);
            resolve(data); // Resolve when tracking is complete
          })
          .catch((error) => {
            console.error("Redirect completion tracking error:", error);
            reject(error);
          });
      } else {
        console.log("No tracker ID found for link code:", linkCode);
        resolve(); // Resolve immediately if no tracker ID
      }
    } catch (error) {
      console.error("Error in handleTrackDidContinue:", error);
      reject(error);
    }
  });
}

function twitterShare() {
  var tweetText = "Check out this awesome content!";
  var hashtags = ["TunnaDuong", "TunnaDuongLinkShortener"]; // Add your desired hashtags here
  var hashtagsString = hashtags.map((tag) => "#" + tag).join(" "); // Converts array to hashtag string
  var url =
    "https://twitter.com/intent/tweet?url=" +
    location.href +
    "&text=" +
    encodeURIComponent(tweetText + " " + hashtagsString);
  window.open(url, "Twitter Share", "width=600,height=400");
}

// JavaScript function to copy the current page URL to the clipboard
function copyPageUrl() {
  var url = window.location.href; // Get the current page URL
  var tempInput = document.createElement("input");
  tempInput.value = url;
  document.body.appendChild(tempInput);
  tempInput.select();
  document.execCommand("copy");
  document.body.removeChild(tempInput);
  alert("Đã copy link: " + url);
}
