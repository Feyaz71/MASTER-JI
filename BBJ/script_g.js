document.addEventListener("DOMContentLoaded", function () {
  const menuIcon = document.querySelector(".hamburger-menu img");
  const sidebar = document.createElement("div");
  sidebar.classList.add("sidebar");
  sidebar.innerHTML = `
      <div class="sidebar-content">
          <button class="close-btn">&times;</button>
          <a href="#">Teacher's Page</a>
          <a href="#">Student's Page</a>
      </div>
  `;
  document.body.appendChild(sidebar);

  menuIcon.addEventListener("click", function () {
      sidebar.classList.add("open");
  });

  document.querySelector(".close-btn").addEventListener("click", function () {
      sidebar.classList.remove("open");
  });

  const buttons = document.querySelectorAll(".button, .buttonhere");
  buttons.forEach(button => {
      button.addEventListener("mousedown", function () {
          this.style.transform = "scale(0.95)";
      });
      button.addEventListener("mouseup", function () {
          this.style.transform = "scale(1)";
      });
      button.addEventListener("mouseleave", function () {
          this.style.transform = "scale(1)";
      });
  });

  document.querySelectorAll(".features, .howitworks, .contact").forEach(link => {
      link.addEventListener("click", function (event) {
          event.preventDefault();
          const sectionID = this.textContent.toLowerCase().replace(/\s+/g, "-");
          const section = document.getElementById(sectionID);
          if (section) {
              window.scrollTo({
                  top: section.offsetTop - 50,
                  behavior: "smooth"
              });
          }
      });
  });
});

const chatbotButton = document.getElementById('chatbotButton');
const chatbotContainer = document.getElementById('chatbotContainer');

// chatbot visibility
chatbotButton.addEventListener('click', function(event) {
    event.preventDefault(); 
    if (chatbotContainer.style.display === 'none') {
        chatbotContainer.style.display = 'block'; // Show the chatbot
    } else {
        chatbotContainer.style.display = 'none'; // Hide the chatbot
    }
});
