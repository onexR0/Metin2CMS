document.addEventListener("DOMContentLoaded", () => {
  function closeAlert(button) {
    const alert = button.closest(".alert-banner")
    if (alert) {
      alert.classList.add("hiding")
      setTimeout(() => {
        alert.remove()
      }, 300)
    }
  }

  const alertBanner = document.getElementById("alertBanner")

  if (alertBanner) {
    const errorAlert = document.querySelector(".alert[data-type='error']")
    const successAlert = document.querySelector(".alert[data-type='success']")
    const warningAlert = document.querySelector(".alert[data-type='warning']")

    if (errorAlert) {
      const message = errorAlert.getAttribute("data-message")
      const banner = document.createElement("div")
      banner.className = "alert-banner"

      const messageSpan = document.createElement("span")
      messageSpan.textContent = message

      const closeBtn = document.createElement("button")
      closeBtn.type = "button"
      closeBtn.className = "alert-banner-close"
      closeBtn.textContent = "×"

      banner.appendChild(messageSpan)
      banner.appendChild(closeBtn)

      closeBtn.addEventListener("click", function () {
        closeAlert(this)
      })
      alertBanner.appendChild(banner)
      setTimeout(() => {
        closeAlert(closeBtn)
      }, 5000)
    }

    if (successAlert) {
      const message = successAlert.getAttribute("data-message")
      const banner = document.createElement("div")
      banner.className = "alert-banner alert-success"

      const messageSpan = document.createElement("span")
      messageSpan.textContent = message

      const closeBtn = document.createElement("button")
      closeBtn.type = "button"
      closeBtn.className = "alert-banner-close"
      closeBtn.textContent = "×"

      banner.appendChild(messageSpan)
      banner.appendChild(closeBtn)

      closeBtn.addEventListener("click", function () {
        closeAlert(this)
      })
      alertBanner.appendChild(banner)
      setTimeout(() => {
        closeAlert(closeBtn)
      }, 5000)
    }

    if (warningAlert) {
      const message = warningAlert.getAttribute("data-message")
      const banner = document.createElement("div")
      banner.className = "alert-banner alert-warning"
      banner.setAttribute("data-permanent", "true")

      const messageSpan = document.createElement("span")
      messageSpan.textContent = message

      const closeBtn = document.createElement("button")
      closeBtn.type = "button"
      closeBtn.className = "alert-banner-close"
      closeBtn.textContent = "×"

      banner.appendChild(messageSpan)
      banner.appendChild(closeBtn)

      closeBtn.addEventListener("click", function () {
        closeAlert(this)
      })
      alertBanner.appendChild(banner)
    }
  }

  document.querySelectorAll(".stats-list strong").forEach((el) => {
    const v = Number.parseInt(el.innerText)
    el.innerText = v.toLocaleString("de-DE")
  })

  const form = document.getElementById("passwordCheck")
  if (form) {
    const pass = document.getElementById("password")
    const pass2 = document.getElementById("password2")
    const errEl = document.getElementById("password-error")

    if (pass && pass2 && errEl) {
      function validatePasswords() {
        if (pass2.value.length > 0) {
          if (pass.value !== pass2.value) {
            errEl.textContent = ""
            pass.classList.add("input-error")
            pass2.classList.add("input-error")
            return false
          } else {
            errEl.textContent = ""
            pass.classList.remove("input-error")
            pass2.classList.remove("input-error")
            return true
          }
        } else {
          errEl.textContent = ""
          pass.classList.remove("input-error")
          pass2.classList.remove("input-error")
          return true
        }
      }

      form.addEventListener("submit", (e) => {
        if (!validatePasswords() && pass2.value.length > 0) {
          e.preventDefault()
          pass2.focus()
        }
      })

      pass2.addEventListener("input", validatePasswords)

      pass2.addEventListener("blur", validatePasswords)

      pass.addEventListener("input", () => {
        if (pass2.value.length > 0) {
          validatePasswords()
        }
      })
    }
  }
})

function toggleSection(element) {
  element.classList.toggle("active")
}

function openModal(modalId) {
  const modal = document.getElementById(modalId)
  if (modal) {
    modal.classList.add("active")
    document.body.style.overflow = "hidden"
  }
}

function closeModal(modalId) {
  const modal = document.getElementById(modalId)
  if (modal) {
    modal.classList.remove("active")
    document.body.style.overflow = ""
  }
}

document.addEventListener("keydown", (e) => {
  if (e.key === "Escape") {
    const activeModal = document.querySelector(".modal.active")
    if (activeModal) {
      closeModal(activeModal.id)
    }
  }
})

document.addEventListener("DOMContentLoaded", () => {
  const menuToggle = document.querySelector(".mobile-menu-toggle")
  const topNav = document.querySelector(".top-nav")
  const body = document.body

  if (menuToggle && topNav) {
    menuToggle.addEventListener("click", () => {
      menuToggle.classList.toggle("active")
      topNav.classList.toggle("mobile-active")
      body.classList.toggle("menu-open")
    })

    document.addEventListener("click", (e) => {
      if (!menuToggle.contains(e.target) && !topNav.contains(e.target)) {
        menuToggle.classList.remove("active")
        topNav.classList.remove("mobile-active")
        body.classList.remove("menu-open")
      }
    })

    topNav.querySelectorAll("a").forEach((link) => {
      link.addEventListener("click", () => {
        menuToggle.classList.remove("active")
        topNav.classList.remove("mobile-active")
        body.classList.remove("menu-open")
      })
    })
  }
})
