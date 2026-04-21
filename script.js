// =========================
// LOADER FIX
// =========================
window.addEventListener("load", () => {

    const loader = document.querySelector(".loader");

    if(loader){
        setTimeout(() => {

            loader.style.opacity = "0";
            loader.style.pointerEvents = "none";

            setTimeout(()=>{
                loader.style.display = "none";
            },600)

        },700)
    }

});


// =========================
// RANDOM HERO BACKGROUND
// =========================

document.addEventListener("DOMContentLoaded", () => {

    const hero = document.querySelector(".hero");

    if(hero){

        const watches = [
        "https://images.unsplash.com/photo-1523275335684-37898b6baf30",
        "https://images.unsplash.com/photo-1547996160-81dfa63595aa",
        "https://images.unsplash.com/photo-1518544889280-9f28d6a90b36",
        "https://images.unsplash.com/photo-1524592094714-0f0654e20314"
        ];

        const random = watches[Math.floor(Math.random()*watches.length)];

        hero.style.backgroundImage =
        "linear-gradient(rgba(0,0,0,.65),rgba(0,0,0,.65)), url("+random+")";

        hero.style.backgroundSize = "cover";
        hero.style.backgroundPosition = "center";

    }

});


// =========================
// CART STORAGE
// =========================

function getCart(){
    return JSON.parse(localStorage.getItem("cart")) || [];
}

function saveCart(cart){
    localStorage.setItem("cart", JSON.stringify(cart));
}


// =========================
// ADD TO CART
// =========================

function addToCart(product_id){
    fetch('add_to_cart.php', {
        method: 'POST',
        headers: {'Content-Type':'application/x-www-form-urlencoded'},
        body: 'product_id=' + product_id
    })
    .then(res => res.text())
    .then(data => {
        if(data === "success"){
            showToast("Added to cart");
        }
    });
}

function showToast(msg){
    let toast = document.createElement("div");
    toast.innerText = msg;
    toast.className = "toast";

    document.body.appendChild(toast);

    setTimeout(()=>{
        toast.remove();
    }, 3000);
}

// =========================
// REMOVE ITEM
// =========================

function removeFromCart(index){

    let cart = getCart();

    cart.splice(index,1);

    saveCart(cart);

    renderCart();

}


// =========================
// CHANGE QUANTITY
// =========================

function changeQty(index, change){

    let cart = getCart();

    cart[index].qty += change;

    if(cart[index].qty <= 0){
        cart.splice(index,1);
    }

    saveCart(cart);

    renderCart();

}


// =========================
// RENDER CART
// =========================

function renderCart(){

    const container = document.querySelector(".cart-container");

    if(!container) return;

    const cart = getCart();

    container.innerHTML = "";

    let total = 0;

    cart.forEach((item,i)=>{

        const qty = item.qty || 1;

        total += item.price * qty;

        const div = document.createElement("div");

        div.className = "cart-item";

        div.innerHTML = `

        <div class="cart-name">${item.name}</div>

        <div class="qty-control">
            <button onclick="changeQty(${i},-1)">-</button>
            <span>${qty}</span>
            <button onclick="changeQty(${i},1)">+</button>
        </div>

        <div class="cart-price">$${item.price * qty}</div>

        <button class="remove-btn" onclick="removeFromCart(${i})">
        Remove
        </button>

        `;

        container.appendChild(div);

    });


    // TOTAL SECTION
    const totalDiv = document.createElement("div");

    totalDiv.className = "cart-total";

    totalDiv.innerHTML = `Total: $${total}`;

    container.appendChild(totalDiv);

}


// =========================
// LOAD CART ON PAGE
// =========================

document.addEventListener("DOMContentLoaded", renderCart);

document.addEventListener("mousemove", e => {

    let glow = document.getElementById("cursor-glow");

    if(!glow){
        glow = document.createElement("div");
        glow.id = "cursor-glow";
        document.body.appendChild(glow);
    }

    glow.style.left = e.clientX + "px";
    glow.style.top = e.clientY + "px";
});