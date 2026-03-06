document.querySelectorAll("a").forEach(link => {
    link.addEventListener("click", function(e){
        e.preventDefault();
        document.querySelector(".form-container").classList.add("fade-out");
        setTimeout(() => {
            window.location = this.href;
        }, 500);
    });
});

