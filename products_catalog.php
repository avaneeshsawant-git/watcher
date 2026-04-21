<?php
function getProductsCatalog()
{
    return [
        1 => [
            "id" => 1,
            "img" => "watch1.jpg",
            "name" => "Obsidian Royale",
            "price" => 3500,
            "discount" => 0,
            "stock" => 1,
            "tagline" => "A polished black dress watch made for evening wear.",
            "description" => "Obsidian Royale pairs a deep black dial with a refined stainless steel case. It is designed for formal outfits, gifting, and everyday luxury without feeling loud.",
            "brand" => "TimeSteal Signature",
            "movement" => "Quartz precision movement",
            "case" => "Stainless steel",
            "strap" => "Black leather strap",
            "water" => "30 meters splash resistant",
            "warranty" => "2 years service warranty"
        ],
        2 => [
            "id" => 2,
            "img" => "watch2.jpg",
            "name" => "Golden Chronos",
            "price" => 4200,
            "discount" => 10,
            "stock" => 1,
            "tagline" => "A gold-toned chronograph with a sharp luxury finish.",
            "description" => "Golden Chronos brings a confident gold finish, readable markers, and chronograph-inspired styling for customers who want a bold premium piece.",
            "brand" => "TimeSteal Heritage",
            "movement" => "Chronograph quartz movement",
            "case" => "Gold plated alloy case",
            "strap" => "Gold tone bracelet",
            "water" => "50 meters water resistant",
            "warranty" => "2 years service warranty"
        ],
        3 => [
            "id" => 3,
            "img" => "watch3.jpg",
            "name" => "Midnight Steel",
            "price" => 3800,
            "discount" => 0,
            "stock" => 1,
            "tagline" => "A minimal steel watch with a midnight dial.",
            "description" => "Midnight Steel is built around a clean dial, sturdy steel case, and versatile styling. It works well for office wear, casual evenings, and daily use.",
            "brand" => "TimeSteal Classic",
            "movement" => "Quartz precision movement",
            "case" => "Brushed stainless steel",
            "strap" => "Stainless steel bracelet",
            "water" => "30 meters splash resistant",
            "warranty" => "2 years service warranty"
        ],
        4 => [
            "id" => 4,
            "img" => "watch4.jpg",
            "name" => "Royal Phantom",
            "price" => 5400,
            "discount" => 0,
            "stock" => 0,
            "tagline" => "A statement watch with a dark royal profile.",
            "description" => "Royal Phantom is a premium showcase piece with dramatic contrast, a sculpted case, and a collector-style presence. This model is currently out of stock.",
            "brand" => "TimeSteal Royal",
            "movement" => "Automatic style display movement",
            "case" => "Polished alloy case",
            "strap" => "Premium black bracelet",
            "water" => "50 meters water resistant",
            "warranty" => "2 years service warranty"
        ],
        5 => [
            "id" => 5,
            "img" => "watch5.jpg",
            "name" => "Platinum Edge",
            "price" => 6100,
            "discount" => 15,
            "stock" => 1,
            "tagline" => "A clean platinum finish for sharp formal styling.",
            "description" => "Platinum Edge is made for customers who prefer bright metal, clean finishing, and premium visual weight. It pairs especially well with suits and formal wear.",
            "brand" => "TimeSteal Executive",
            "movement" => "Quartz precision movement",
            "case" => "Platinum tone stainless steel",
            "strap" => "Platinum tone bracelet",
            "water" => "50 meters water resistant",
            "warranty" => "2 years service warranty"
        ],
        6 => [
            "id" => 6,
            "img" => "watch6.jpg",
            "name" => "Aurora Chronograph",
            "price" => 4800,
            "discount" => 0,
            "stock" => 1,
            "tagline" => "Chronograph styling with a bright luxury personality.",
            "description" => "Aurora Chronograph combines sporty sub-dial styling with a polished finish. It is a versatile option for customers who want a watch that feels energetic but refined.",
            "brand" => "TimeSteal Sport Luxe",
            "movement" => "Chronograph quartz movement",
            "case" => "Stainless steel",
            "strap" => "Textured leather strap",
            "water" => "50 meters water resistant",
            "warranty" => "2 years service warranty"
        ],
        7 => [
            "id" => 7,
            "img" => "watch7.jpg",
            "name" => "Black Nebula",
            "price" => 5200,
            "discount" => 5,
            "stock" => 1,
            "tagline" => "A dark modern watch with a bold wrist presence.",
            "description" => "Black Nebula is built for customers who like matte dark styling, a strong case shape, and a modern luxury feel that stands out without bright colors.",
            "brand" => "TimeSteal Nightfall",
            "movement" => "Quartz precision movement",
            "case" => "Black coated stainless steel",
            "strap" => "Black steel bracelet",
            "water" => "50 meters water resistant",
            "warranty" => "2 years service warranty"
        ],
        8 => [
            "id" => 8,
            "img" => "watch8.jpg",
            "name" => "Emerald Prestige",
            "price" => 4700,
            "discount" => 8,
            "stock" => 1,
            "tagline" => "A premium green dial watch with classic polish.",
            "description" => "Emerald Prestige uses a rich green tone and refined metal detailing to create a distinctive dress watch for customers who want color with sophistication.",
            "brand" => "TimeSteal Prestige",
            "movement" => "Quartz precision movement",
            "case" => "Stainless steel",
            "strap" => "Stainless steel bracelet",
            "water" => "30 meters splash resistant",
            "warranty" => "2 years service warranty"
        ],
        9 => [
            "id" => 9,
            "img" => "watch9.jpg",
            "name" => "Titanium Horizon",
            "price" => 5600,
            "discount" => 20,
            "stock" => 1,
            "tagline" => "A light, resilient watch inspired by travel and open skies.",
            "description" => "Titanium Horizon focuses on comfort, durability, and a crisp premium look. It is a strong pick for long daily wear and travel-ready styling.",
            "brand" => "TimeSteal Horizon",
            "movement" => "Quartz precision movement",
            "case" => "Titanium tone stainless steel",
            "strap" => "Titanium tone bracelet",
            "water" => "50 meters water resistant",
            "warranty" => "2 years service warranty"
        ]
    ];
}

function getProductById($id)
{
    $catalog = getProductsCatalog();
    if (!isset($catalog[$id])) {
        return null;
    }

    return enrichProduct($catalog[$id]);
}

function enrichProduct($product)
{
    $id = (int) $product["id"];
    $catalog = getProductsCatalog();
    $names = array_column($catalog, "name");
    $collectionPosition = array_search($product["name"], $names, true) + 1;

    $product["sku"] = "TS-" . str_pad((string) $id, 4, "0", STR_PAD_LEFT);
    $product["dial"] = match ($id) {
        2 => "Sunburst champagne dial with baton markers",
        4 => "Deep charcoal dial with dramatic contrast markers",
        8 => "Emerald green dial with polished hour markers",
        default => "Clean luxury dial with polished hour markers"
    };
    $product["glass"] = "Scratch-resistant mineral crystal";
    $product["dimensions"] = $id % 2 === 0 ? "42 mm case, 11 mm thickness" : "40 mm case, 10 mm thickness";
    $product["weight"] = $id % 3 === 0 ? "132 g approx." : "118 g approx.";
    $product["clasp"] = str_contains(strtolower($product["strap"]), "leather") ? "Pin buckle closure" : "Fold-over deployment clasp";
    $product["availability"] = (int) $product["stock"] > 0 ? "In stock and ready to ship" : "Temporarily out of stock";
    $product["delivery"] = "Free insured delivery in 3-5 business days";
    $product["returns"] = "7-day return window after delivery";
    $product["package"] = "Watch, premium box, warranty card, care cloth";
    $product["collection_rank"] = "Collection piece " . $collectionPosition . " of " . count($catalog);
    $product["highlights"] = [
        $product["availability"],
        $product["delivery"],
        $product["warranty"],
        "Secure checkout with cart support"
    ];
    $product["sample_reviews"] = getSampleReviews($id);

    return $product;
}

function getSampleReviews($id)
{
    $reviews = [
        1 => [
            ["name" => "Aarav Mehta", "rating" => 5, "date" => "12 Mar 2026", "text" => "The black dial looks much richer in person. It feels elegant with formal shirts and the strap softened after a day."],
            ["name" => "Neha Kapoor", "rating" => 4, "date" => "25 Feb 2026", "text" => "Bought it as a gift and the packaging felt premium. The watch has a clean, classy look."],
            ["name" => "Rohan Iyer", "rating" => 5, "date" => "08 Jan 2026", "text" => "Lightweight, comfortable, and easy to read. Exactly the understated style I wanted."]
        ],
        2 => [
            ["name" => "Karan Shah", "rating" => 5, "date" => "03 Apr 2026", "text" => "The gold finish stands out without looking cheap. Great for weddings and evening events."],
            ["name" => "Ishita Rao", "rating" => 4, "date" => "14 Mar 2026", "text" => "Looks bold and premium. The bracelet fit needed one adjustment, then it was perfect."],
            ["name" => "Dev Patel", "rating" => 5, "date" => "22 Feb 2026", "text" => "The discount made it a really good buy. It has a strong wrist presence."]
        ],
        3 => [
            ["name" => "Sameer Nair", "rating" => 5, "date" => "18 Mar 2026", "text" => "Very versatile. I wear it to work almost every day and it still looks new."],
            ["name" => "Tanya Singh", "rating" => 4, "date" => "01 Mar 2026", "text" => "Minimal and sharp. The steel finish pairs well with everything."],
            ["name" => "Aditya Menon", "rating" => 5, "date" => "09 Feb 2026", "text" => "Good weight, clean dial, and the case finishing feels more expensive than the price."]
        ],
        4 => [
            ["name" => "Vihaan Arora", "rating" => 5, "date" => "28 Mar 2026", "text" => "Managed to get one before it went out of stock. It has a collector-style look."],
            ["name" => "Meera Joshi", "rating" => 4, "date" => "11 Feb 2026", "text" => "The dark profile is gorgeous. It is definitely more of a statement piece."],
            ["name" => "Kabir Sethi", "rating" => 5, "date" => "20 Jan 2026", "text" => "The case shape is the highlight. People asked me about it twice in one evening."]
        ],
        5 => [
            ["name" => "Ananya Das", "rating" => 5, "date" => "10 Apr 2026", "text" => "The platinum tone is crisp and formal. It looks excellent with business wear."],
            ["name" => "Harsh Verma", "rating" => 5, "date" => "17 Mar 2026", "text" => "Feels premium from the first unboxing. The bracelet has a nice shine."],
            ["name" => "Priya Malhotra", "rating" => 4, "date" => "06 Feb 2026", "text" => "Very polished and gift-worthy. The dial is clean and easy to read."]
        ],
        6 => [
            ["name" => "Yash Bhatia", "rating" => 5, "date" => "02 Apr 2026", "text" => "Sporty but still classy. The chronograph styling makes it feel energetic."],
            ["name" => "Ritika Sen", "rating" => 4, "date" => "13 Mar 2026", "text" => "Comfortable strap and a nice bright finish. Works with casual outfits too."],
            ["name" => "Nikhil Jain", "rating" => 5, "date" => "26 Feb 2026", "text" => "Good dial detail, neat finishing, and the size feels balanced."]
        ],
        7 => [
            ["name" => "Arjun Gill", "rating" => 5, "date" => "21 Mar 2026", "text" => "The black coating looks aggressive and modern. Perfect if you like darker watches."],
            ["name" => "Sara Dsouza", "rating" => 4, "date" => "03 Mar 2026", "text" => "Bought it for my brother. He loved the matte look and the weight."],
            ["name" => "Mohit Rana", "rating" => 5, "date" => "15 Jan 2026", "text" => "Premium feel, good contrast on the dial, and the bracelet feels sturdy."]
        ],
        8 => [
            ["name" => "Avni Kulkarni", "rating" => 5, "date" => "08 Apr 2026", "text" => "The green dial is beautiful. It catches light nicely without being too flashy."],
            ["name" => "Manav Khanna", "rating" => 4, "date" => "12 Mar 2026", "text" => "Different from the usual black and silver watches. Looks refined."],
            ["name" => "Pooja Sinha", "rating" => 5, "date" => "19 Feb 2026", "text" => "Elegant color, solid bracelet, and great packaging. Very happy with it."]
        ],
        9 => [
            ["name" => "Dhruv Reddy", "rating" => 5, "date" => "01 Apr 2026", "text" => "Comfortable for long wear and the discount makes it a strong value pick."],
            ["name" => "Kriti Bansal", "rating" => 5, "date" => "09 Mar 2026", "text" => "It feels light but not flimsy. The horizon styling is clean and modern."],
            ["name" => "Sahil Thomas", "rating" => 4, "date" => "30 Jan 2026", "text" => "Good travel watch. The metal tone looks premium and does not feel heavy."]
        ]
    ];

    return $reviews[$id] ?? [];
}

function getFinalPrice($product)
{
    if ((int) $product["discount"] <= 0) {
        return (int) $product["price"];
    }

    return (int) round($product["price"] - ($product["price"] * $product["discount"] / 100));
}
