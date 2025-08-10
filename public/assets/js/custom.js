(function () {
  "use strict";

  /* page loader */
  
  function hideLoader() {
    const loader = document.getElementById("loader");
    if (loader) {
      loader.classList.add("d-none");
    }
  }

  // Safe checkbox setter for switcher elements (prevents null errors on mobile)
  function safeSetChecked(selector, checked) {
    const element = document.querySelector(selector);
    if (element) {
      element.checked = checked;
    }
  }

  window.addEventListener("load", hideLoader);
  /* page loader */

  /* tooltip */
  const tooltipTriggerList = document.querySelectorAll(
    '[data-bs-toggle="tooltip"]'
  );
  const tooltipList = [...tooltipTriggerList].map(
    (tooltipTriggerEl) => new bootstrap.Tooltip(tooltipTriggerEl)
  );

  /* popover  */
  const popoverTriggerList = document.querySelectorAll(
    '[data-bs-toggle="popover"]'
  );
  const popoverList = [...popoverTriggerList].map(
    (popoverTriggerEl) => new bootstrap.Popover(popoverTriggerEl)
  );

  //switcher color pickers
  const pickrContainerPrimary = document.querySelector(
    ".pickr-container-primary"
  );
  const themeContainerPrimary = document.querySelector(
    ".theme-container-primary"
  );
  const pickrContainerBackground = document.querySelector(
    ".pickr-container-background"
  );
  const themeContainerBackground = document.querySelector(
    ".theme-container-background"
  );

  /* for theme primary */
  const nanoThemes = [
    [
      "nano",
      {
        defaultRepresentation: "RGB",
        components: {
          preview: true,
          opacity: false,
          hue: true,

          interaction: {
            hex: false,
            rgba: true,
            hsva: false,
            input: true,
            clear: false,
            save: false,
          },
        },
      },
    ],
  ];
  const nanoButtons = [];
  let nanoPickr = null;
  for (const [theme, config] of nanoThemes) {
    const button = document.createElement("button");
    button.innerHTML = theme;
    nanoButtons.push(button);

    button.addEventListener("click", () => {
      const el = document.createElement("p");
      // Safety check before appendChild
      if (pickrContainerPrimary) {
        pickrContainerPrimary.appendChild(el);
      }

      /* Delete previous instance */
      if (nanoPickr) {
        nanoPickr.destroyAndRemove();
      }

      /* Apply active class */
      for (const btn of nanoButtons) {
        btn.classList[btn === button ? "add" : "remove"]("active");
      }

      /* Create fresh instance */
      // Safety check for element before creating Pickr
      if (el && pickrContainerPrimary && pickrContainerPrimary.contains(el)) {
        nanoPickr = new Pickr(
          Object.assign(
            {
              el,
              theme,
              default: "#3366ff",
            },
            config
          )
        );
      } else {
        // Pickr element not properly initialized, skipping (silent mode)
        return;
      }

      /* Set events */
      nanoPickr.on("changestop", (source, instance) => {
        let color = instance.getColor().toRGBA();
        let html = document.querySelector("html");
        html.style.setProperty(
          "--primary-rgb",
          `${Math.floor(color[0])}, ${Math.floor(color[1])}, ${Math.floor(
            color[2]
          )}`
        );
        /* theme color picker */
        localStorage.setItem(
          "primaryRGB",
          `${Math.floor(color[0])}, ${Math.floor(color[1])}, ${Math.floor(
            color[2]
          )}`
        );
        updateColors();
      });
    });

    // Check if themeContainerPrimary exists before appendChild
    if (themeContainerPrimary) {
      themeContainerPrimary.appendChild(button);
    }
  }
  
  // Check if nanoButtons exists and has elements before clicking
  if (nanoButtons && nanoButtons.length > 0) {
    nanoButtons[0].click();
  }
  /* for theme primary */

  /* for theme background */
  const nanoThemes1 = [
    [
      "nano",
      {
        defaultRepresentation: "RGB",
        components: {
          preview: true,
          opacity: false,
          hue: true,

          interaction: {
            hex: false,
            rgba: true,
            hsva: false,
            input: true,
            clear: false,
            save: false,
          },
        },
      },
    ],
  ];
  const nanoButtons1 = [];
  let nanoPickr1 = null;
  for (const [theme, config] of nanoThemes) {
    const button = document.createElement("button");
    button.innerHTML = theme;
    nanoButtons1.push(button);

    button.addEventListener("click", () => {
      const el = document.createElement("p");
      // Safety check before appendChild
      if (pickrContainerBackground) {
        pickrContainerBackground.appendChild(el);
      }

      /* Delete previous instance */
      if (nanoPickr1) {
        nanoPickr1.destroyAndRemove();
      }

      /* Apply active class */
      for (const btn of nanoButtons) {
        btn.classList[btn === button ? "add" : "remove"]("active");
      }

      /* Create fresh instance */
      nanoPickr1 = new Pickr(
        Object.assign(
          {
            el,
            theme,
            default: "#3366ff",
          },
          config
        )
      );

      /* Set events */
      nanoPickr1.on("changestop", (source, instance) => {
        let color = instance.getColor().toRGBA();
        let html = document.querySelector("html");
        html.style.setProperty(
          "--body-bg-rgb",
          `${color[0]}, ${color[1]}, ${color[2]}`
        );
        document
          .querySelector("html")
          .style.setProperty(
            "--body-bg-rgb2",
            `${color[0] + 14}, ${color[1] + 14}, ${color[2] + 14}`
          );
        document
          .querySelector("html")
          .style.setProperty(
            "--light-rgb",
            `${color[0] + 14}, ${color[1] + 14}, ${color[2] + 14}`
          );
        document
          .querySelector("html")
          .style.setProperty(
            "--form-control-bg",
            `rgb(${color[0] + 14}, ${color[1] + 14}, ${color[2] + 14})`
          );
        localStorage.removeItem("bgtheme");
        updateColors();
        html.setAttribute("data-theme-mode", "dark");
        html.setAttribute("data-menu-styles", "dark");
        html.setAttribute("data-header-styles", "dark");
        safeSetChecked("#switcher-dark-theme", true);
        localStorage.setItem(
          "bodyBgRGB",
          `${color[0]}, ${color[1]}, ${color[2]}`
        );
        localStorage.setItem(
          "bodylightRGB",
          `${color[0] + 14}, ${color[1] + 14}, ${color[2] + 14}`
        );
      });
    });
    // Safety check before appendChild
    if (themeContainerBackground) {
      themeContainerBackground.appendChild(button);
    }
  }
  nanoButtons1[0].click();
  /* for theme background */

  /* header theme toggle */
  function toggleTheme() {
    let html = document.querySelector("html");
    if (html.getAttribute("data-theme-mode") === "dark") {
      html.setAttribute("data-theme-mode", "light");
      html.setAttribute("data-header-styles", "light");
      html.setAttribute("data-menu-styles", "light");
      if (!localStorage.getItem("primaryRGB")) {
        html.setAttribute("style", "");
      }
      html.removeAttribute("data-bg-theme");
      safeSetChecked("#switcher-light-theme", true);
      safeSetChecked("#switcher-menu-light", true);
      document
        .querySelector("html")
        .style.removeProperty("--body-bg-rgb", localStorage.bodyBgRGB);
      checkOptions();
      html.style.removeProperty("--body-bg-rgb2");
      html.style.removeProperty("--light-rgb");
      html.style.removeProperty("--form-control-bg");
      html.style.removeProperty("--input-border");
      safeSetChecked("#switcher-header-light", true);
      safeSetChecked("#switcher-menu-light", true);
      safeSetChecked("#switcher-light-theme", true);
      safeSetChecked("#switcher-background4", false);
      safeSetChecked("#switcher-background3", false);
      safeSetChecked("#switcher-background2", false);
      safeSetChecked("#switcher-background1", false);
      safeSetChecked("#switcher-background", false);
      localStorage.removeItem("dayonedarktheme");
      localStorage.removeItem("dayoneMenu");
      localStorage.removeItem("dayoneHeader");
      localStorage.removeItem("bodylightRGB");
      localStorage.removeItem("bodyBgRGB");
      if (localStorage.getItem("dayonelayout") != "horizontal") {
        html.setAttribute("data-menu-styles", "dark");
      }
      html.setAttribute("data-header-styles", "light");
    } else {
      html.setAttribute("data-theme-mode", "dark");
      html.setAttribute("data-header-styles", "dark");
      if (!localStorage.getItem("primaryRGB")) {
        html.setAttribute("style", "");
      }
      html.setAttribute("data-menu-styles", "dark");
      safeSetChecked("#switcher-dark-theme", true);
      safeSetChecked("#switcher-menu-dark", true);
      safeSetChecked("#switcher-header-dark", true);
      checkOptions();
      safeSetChecked("#switcher-menu-dark", true);
      safeSetChecked("#switcher-header-dark", true);
      safeSetChecked("#switcher-dark-theme", true);
      safeSetChecked("#switcher-background4", false);
      safeSetChecked("#switcher-background3", false);
      safeSetChecked("#switcher-background2", false);
      safeSetChecked("#switcher-background1", false);
      safeSetChecked("#switcher-background", false);
      localStorage.setItem("dayonedarktheme", "true");
      localStorage.setItem("dayoneMenu", "dark");
      localStorage.setItem("dayoneHeader", "dark");
      localStorage.removeItem("bodylightRGB");
      localStorage.removeItem("bodyBgRGB");
    }
  }
  let layoutSetting = document.querySelector(".layout-setting");
  if (layoutSetting) {
    layoutSetting.addEventListener("click", toggleTheme);
  }
  /* header theme toggle */

  /* Choices JS */
  document.addEventListener("DOMContentLoaded", function () {
    var genericExamples = document.querySelectorAll("[data-trigger]");
    for (let i = 0; i < genericExamples.length; ++i) {
      var element = genericExamples[i];
      new Choices(element, {
        allowHTML: true,
        placeholderValue: "This is a placeholder set in the config",
        searchPlaceholderValue: "Search",
      });
    }
  });
  /* Choices JS */

  /* footer year */
  const yearElement = document.getElementById("year");
  if (yearElement) {
    yearElement.innerHTML = new Date().getFullYear();
  }
  /* footer year */

  /* node waves */
  Waves.attach(".btn-wave", ["waves-light"]);
  Waves.init();
  /* node waves */

  /* card with close button */
  let DIV_CARD = ".card";
  let cardRemoveBtn = document.querySelectorAll(
    '[data-bs-toggle="card-remove"]'
  );
  cardRemoveBtn.forEach((ele) => {
    ele.addEventListener("click", function (e) {
      e.preventDefault();
      let $this = this;
      let card = $this.closest(DIV_CARD);
      card.remove();
      return false;
    });
  });
  /* card with close button */

  /* card with fullscreen */
  let cardFullscreenBtn = document.querySelectorAll(
    '[data-bs-toggle="card-fullscreen"]'
  );
  cardFullscreenBtn.forEach((ele) => {
    ele.addEventListener("click", function (e) {
      let $this = this;
      let card = $this.closest(DIV_CARD);
      card.classList.toggle("card-fullscreen");
      card.classList.remove("card-collapsed");
      e.preventDefault();
      return false;
    });
  });
  /* card with fullscreen */

  /* count-up */
  var i = 1;
  setInterval(() => {
    document.querySelectorAll(".count-up").forEach((ele) => {
      if (ele.getAttribute("data-count") >= i) {
        i = i + 1;
        ele.innerText = i;
      }
    });
  }, 10);
  /* count-up */

  /* back to top */
  const scrollToTop = document.querySelector(".scrollToTop");
  
  // Only set up scroll-to-top if element exists
  if (scrollToTop) {
    const $rootElement = document.documentElement;
    const $body = document.body;
    window.onscroll = () => {
      const scrollTop = window.scrollY || window.pageYOffset;
      const clientHt = $rootElement.scrollHeight - $rootElement.clientHeight;
      if (window.scrollY > 100) {
        scrollToTop.style.display = "flex";
      } else {
        scrollToTop.style.display = "none";
      }
    };
    scrollToTop.onclick = () => {
      window.scrollTo(0, 0);
    };
  }
  /* back to top */

  /* header dropdowns scroll */
  var myHeaderShortcut = document.getElementById("header-shortcut-scroll");
  if (myHeaderShortcut) {
    new SimpleBar(myHeaderShortcut, { autoHide: true });
  }

  var myHeadernotification = document.getElementById(
    "header-notification-scroll"
  );
  if (myHeadernotification) {
    new SimpleBar(myHeadernotification, { autoHide: true });
  }

  var myHeaderCart = document.getElementById("header-cart-items-scroll");
  if (myHeaderCart) {
    new SimpleBar(myHeaderCart, { autoHide: true });
  }
  /* header dropdowns scroll */
})();

/* full screen */
var elem = document.documentElement;
function openFullscreen() {
  let open = document.querySelector(".full-screen-open");
  let close = document.querySelector(".full-screen-close");

  // Only proceed if fullscreen elements exist
  if (!open || !close) {
    return;
  }

  if (
    !document.fullscreenElement &&
    !document.webkitFullscreenElement &&
    !document.msFullscreenElement
  ) {
    if (elem.requestFullscreen) {
      elem.requestFullscreen();
    } else if (elem.webkitRequestFullscreen) {
      /* Safari */
      elem.webkitRequestFullscreen();
    } else if (elem.msRequestFullscreen) {
      /* IE11 */
      elem.msRequestFullscreen();
    }
    close.classList.add("d-block");
    close.classList.remove("d-none");
    open.classList.add("d-none");
  } else {
    if (document.exitFullscreen) {
      document.exitFullscreen();
    } else if (document.webkitExitFullscreen) {
      /* Safari */
      document.webkitExitFullscreen();
    } else if (document.msExitFullscreen) {
      /* IE11 */
      document.msExitFullscreen();
    }
    close.classList.remove("d-block");
    open.classList.remove("d-none");
    close.classList.add("d-none");
    open.classList.add("d-block");
  }
}
/* full screen */

/* toggle switches */
let customSwitch = document.querySelectorAll(".toggle");
customSwitch.forEach((e) =>
  e.addEventListener("click", () => {
    e.classList.toggle("on");
  })
);
/* toggle switches */

/* header dropdown close button */

/* for cart dropdown */
const headerbtn = document.querySelectorAll(".dropdown-item-close");
headerbtn.forEach((button) => {
  button.addEventListener("click", (e) => {
    e.preventDefault();
    e.stopPropagation();
    button.parentNode.parentNode.parentNode.parentNode.parentNode.remove();
    const cartData = document.getElementById("cart-data");
    const cartIconBadge = document.getElementById("cart-icon-badge");
    
    if (cartData) {
      cartData.innerText = `${
        document.querySelectorAll(".dropdown-item-close").length
      } Items`;
    }
    
    if (cartIconBadge) {
      cartIconBadge.innerText = `${
        document.querySelectorAll(".dropdown-item-close").length
      }`;
    }
    if (document.querySelectorAll(".dropdown-item-close").length == 0) {
      let elementHide = document.querySelector(".empty-header-item");
      let elementShow = document.querySelector(".empty-item");
      elementHide.classList.add("d-none");
      elementShow.classList.remove("d-none");
    }
  });
});
/* for cart dropdown */

/* for notifications dropdown */
const headerbtn1 = document.querySelectorAll(".dropdown-item-close1");
headerbtn1.forEach((button) => {
  button.addEventListener("click", (e) => {
    e.preventDefault();
    e.stopPropagation();
    button.parentNode.parentNode.parentNode.remove();
    const notificationData = document.getElementById("notifiation-data");
    
    if (notificationData) {
      notificationData.innerText = `${
        document.querySelectorAll(".dropdown-item-close1").length
      } Unread`;
    }
    // document.getElementById("notification-icon-badge").innerText = `${
    //   document.querySelectorAll(".dropdown-item-close1").length
    // }`;
    if (document.querySelectorAll(".dropdown-item-close1").length == 0) {
      let elementHide1 = document.querySelector(".empty-header-item1");
      let elementShow1 = document.querySelector(".empty-item1");
      elementHide1.classList.add("d-none");
      elementShow1.classList.remove("d-none");
    }
  });
});
/* for notifications dropdown */
