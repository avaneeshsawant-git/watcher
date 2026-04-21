document.querySelectorAll("a").forEach(link => {
    link.addEventListener("click", function(e){
        if(this.href && !this.href.includes("#")){
            e.preventDefault();
            document.body.classList.add("fade-out");

            setTimeout(()=>{
                window.location = this.href;
            }, 400);
        }
    });
});