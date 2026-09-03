const waPhone = "27638592102";

// Full Catalog Mapping with Complete Official iPhone Color Options
// Legacy generations (13 and older) carry an "originalPrice" per storage tier to
// power the clearance "SALE" ribbon. Gen 17 is flagged as the newest arrival.
const catalog = [
  // --- IPHONE 17 SERIES ---
  {
    id: "17pm",
    name: "iPhone 17 Pro Max",
    gen: "17",
    colors: [
      { name: "Titanium Gray", hex: "#3f4042", img: "images/iphone-17-pro-max.jpeg" },
      { name: "Copper Amber", hex: "#d06232", img: "images/iphone-17-pro-max copy.jpeg" },
      { name: "Silver Titanium", hex: "#e2e4e5", img: "images/iphone-17-pro-max.jpeg" },
      { name: "Dark Titanium", hex: "#2c2d2e", img: "images/iphone-17-pro-max.jpeg" }
    ],
    display: "6.9\" Super Retina XDR ProMotion",
    chip: "A19 Pro Bionic",
    camera: "48MP Triple Lens + 5x Telephoto",
    battery: "Up to 33 hrs video",
    storageOptions: [
      { size: "256GB", price: 24900 },
      { size: "512GB", price: 28400 },
      { size: "1TB", price: 32900 }
    ]
  },
  {
    id: "17p",
    name: "iPhone 17 Pro",
    gen: "17",
    colors: [
      { name: "Titanium Gray", hex: "#3f4042", img: "images/iphone-17-pro.jpeg" },
      { name: "Copper Amber", hex: "#d06232", img: "images/iphone-17-pro copy.jpeg" },
      { name: "Silver Titanium", hex: "#e2e4e5", img: "images/iphone-17-pro.jpeg" },
      { name: "Dark Titanium", hex: "#2c2d2e", img: "images/iphone-17-pro.jpeg" }
    ],
    display: "6.3\" Super Retina XDR ProMotion",
    chip: "A19 Pro Bionic",
    camera: "48MP Triple Lens",
    battery: "Up to 29 hrs video",
    storageOptions: [
      { size: "128GB", price: 21900 },
      { size: "256GB", price: 24500 },
      { size: "512GB", price: 28900 }
    ]
  },
  {
    id: "17plus",
    name: "iPhone 17 Plus",
    gen: "17",
    colors: [
      { name: "Mist Blue", hex: "#c4dbe0", img: "images/iphone-17-Plus.jpeg" },
      { name: "Soft Pink", hex: "#e8cfd8", img: "images/iphone-17-Plus copy.jpeg" },
      { name: "Sage Green", hex: "#b8c4b8", img: "images/iphone-17-Plus.jpeg" },
      { name: "Black", hex: "#1f2022", img: "images/iphone-17-Plus.jpeg" },
      { name: "White", hex: "#f8f6f0", img: "images/iphone-17-Plus.jpeg" }
    ],
    display: "6.7\" Super Retina XDR",
    chip: "A19 Bionic",
    camera: "48MP Dual Fusion Camera",
    battery: "Up to 30 hrs video",
    storageOptions: [
      { size: "128GB", price: 18500 },
      { size: "256GB", price: 20900 },
      { size: "512GB", price: 24900 }
    ]
  },
  {
    id: "17base",
    name: "iPhone 17",
    gen: "17",
    colors: [
      { name: "Mist Blue", hex: "#c4dbe0", img: "images/iphone-17.jpeg" },
      { name: "Soft Pink", hex: "#e8cfd8", img: "images/iphone-17 copy.jpeg" },
      { name: "Sage Green", hex: "#b8c4b8", img: "images/iphone-17.jpeg" },
      { name: "Black", hex: "#1f2022", img: "images/iphone-17.jpeg" },
      { name: "White", hex: "#f8f6f0", img: "images/iphone-17.jpeg" }
    ],
    display: "6.1\" Super Retina XDR",
    chip: "A19 Bionic",
    camera: "48MP Dual Fusion Camera",
    battery: "Up to 26 hrs video",
    storageOptions: [
      { size: "128GB", price: 15900 },
      { size: "256GB", price: 17900 },
      { size: "512GB", price: 21500 }
    ]
  },

  // --- IPHONE 16 SERIES ---
  {
    id: "16pm",
    name: "iPhone 16 Pro Max",
    gen: "16",
    colors: [
      { name: "Desert Titanium", hex: "#c2a991", img: "images/iphone-16-pro-Max.jpeg" },
      { name: "Natural Titanium", hex: "#babbb8", img: "images/iphone-16-pro-Max copy.jpeg" },
      { name: "White Titanium", hex: "#f8f6f0", img: "images/iphone-16-pro-Max.jpeg" },
      { name: "Black Titanium", hex: "#3c3b37", img: "images/iphone-16-pro-Max.jpeg" }
    ],
    display: "6.9\" Super Retina XDR 120Hz",
    chip: "A18 Pro Bionic",
    camera: "48MP Fusion + 5x Optical",
    battery: "Up to 33 hrs video",
    storageOptions: [
      { size: "256GB", price: 21500 },
      { size: "512GB", price: 24900 },
      { size: "1TB", price: 28900 }
    ]
  },
  {
    id: "16p",
    name: "iPhone 16 Pro",
    gen: "16",
    colors: [
      { name: "Desert Titanium", hex: "#c2a991", img: "images/iphone-16-pro.jpeg" },
      { name: "Natural Titanium", hex: "#babbb8", img: "images/iphone-16-pro copy.jpeg" },
      { name: "White Titanium", hex: "#f8f6f0", img: "images/iphone-16-pro.jpeg" },
      { name: "Black Titanium", hex: "#3c3b37", img: "images/iphone-16-pro.jpeg" }
    ],
    display: "6.3\" Super Retina XDR 120Hz",
    chip: "A18 Pro Bionic",
    camera: "48MP Fusion + 3x Optical",
    battery: "Up to 27 hrs video",
    storageOptions: [
      { size: "128GB", price: 18900 },
      { size: "256GB", price: 21200 },
      { size: "512GB", price: 25400 }
    ]
  },
  {
    id: "16plus",
    name: "iPhone 16 Plus",
    gen: "16",
    colors: [
      { name: "Ultramarine", hex: "#4862a3", img: "images/iphone-16-Plus.jpeg" },
      { name: "Teal", hex: "#a4cbbd", img: "images/iphone-16-Plus copy.jpeg" },
      { name: "Pink", hex: "#e8cfd8", img: "images/iphone-16-Plus.jpeg" },
      { name: "White", hex: "#f8f6f0", img: "images/iphone-16-Plus.jpeg" },
      { name: "Black", hex: "#1f2022", img: "images/iphone-16-Plus.jpeg" }
    ],
    display: "6.7\" Super Retina XDR",
    chip: "A18 Bionic",
    camera: "48MP Dual Lens System",
    battery: "Up to 27 hrs video",
    storageOptions: [
      { size: "128GB", price: 15500 },
      { size: "256GB", price: 17800 },
      { size: "512GB", price: 21900 }
    ]
  },
  {
    id: "16base",
    name: "iPhone 16",
    gen: "16",
    colors: [
      { name: "Ultramarine", hex: "#4862a3", img: "images/iphone-16.jpeg" },
      { name: "Teal", hex: "#a4cbbd", img: "images/iphone-16 copy.jpeg" },
      { name: "Pink", hex: "#e8cfd8", img: "images/iphone-16.jpeg" },
      { name: "White", hex: "#f8f6f0", img: "images/iphone-16.jpeg" },
      { name: "Black", hex: "#1f2022", img: "images/iphone-16.jpeg" }
    ],
    display: "6.1\" Super Retina XDR",
    chip: "A18 Bionic",
    camera: "48MP Dual Lens System",
    battery: "Up to 22 hrs video",
    storageOptions: [
      { size: "128GB", price: 13900 },
      { size: "256GB", price: 15900 },
      { size: "512GB", price: 21500 }
    ]
  },

  // --- IPHONE 15 SERIES ---
  {
    id: "15pm",
    name: "iPhone 15 Pro Max",
    gen: "15",
    colors: [
      { name: "Natural Titanium", hex: "#b8b4ab", img: "images/iphone-15-pro Max.jpeg" },
      { name: "Blue Titanium", hex: "#2f3843", img: "images/iphone-15-pro Max copy.jpeg" },
      { name: "White Titanium", hex: "#f2f3f5", img: "images/iphone-15-pro Max.jpeg" },
      { name: "Black Titanium", hex: "#3b3b3c", img: "images/iphone-15-pro Max.jpeg" }
    ],
    display: "6.7\" Titanium Super Retina XDR",
    chip: "A17 Pro Bionic",
    camera: "48MP Triple Lens + 5x Optical",
    battery: "Up to 29 hrs video",
    storageOptions: [
      { size: "256GB", price: 17900 },
      { size: "512GB", price: 20500 },
      { size: "1TB", price: 23900 }
    ]
  },
  {
    id: "15p",
    name: "iPhone 15 Pro",
    gen: "15",
    colors: [
      { name: "Natural Titanium", hex: "#b8b4ab", img: "images/iphone-15-pro.jpeg" },
      { name: "Blue Titanium", hex: "#2f3843", img: "images/iphone-15-pro copy.jpeg" },
      { name: "White Titanium", hex: "#f2f3f5", img: "images/iphone-15-pro.jpeg" },
      { name: "Black Titanium", hex: "#3b3b3c", img: "images/iphone-15-pro.jpeg" }
    ],
    display: "6.1\" Titanium Super Retina XDR",
    chip: "A17 Pro Bionic",
    camera: "48MP Triple Lens + 3x Optical",
    battery: "Up to 23 hrs video",
    storageOptions: [
      { size: "128GB", price: 14900 },
      { size: "256GB", price: 16900 },
      { size: "512GB", price: 19900 }
    ]
  },
  {
    id: "15plus",
    name: "iPhone 15 Plus",
    gen: "15",
    colors: [
      { name: "Yellow", hex: "#e7e0c4", img: "images/iphone-15-plus.jpeg" },
      { name: "Pink", hex: "#e3c8d0", img: "images/iphone-15-plus copy.jpeg" },
      { name: "Blue", hex: "#c9d8e0", img: "images/iphone-15-plus.jpeg" },
      { name: "Green", hex: "#d3e0d7", img: "images/iphone-15-plus.jpeg" },
      { name: "Black", hex: "#343538", img: "images/iphone-15-plus.jpeg" }
    ],
    display: "6.7\" Dynamic Island Display",
    chip: "A16 Bionic",
    camera: "48MP Dual Main Lens",
    battery: "Up to 26 hrs video",
    storageOptions: [
      { size: "128GB", price: 12900 },
      { size: "256GB", price: 14500 }
    ]
  },
  {
    id: "15base",
    name: "iPhone 15",
    gen: "15",
    colors: [
      { name: "Yellow", hex: "#e7e0c4", img: "images/iphone-15.jpeg" },
      { name: "Pink", hex: "#e3c8d0", img: "images/iphone-15 copy.jpeg" },
      { name: "Blue", hex: "#c9d8e0", img: "images/iphone-15.jpeg" },
      { name: "Green", hex: "#d3e0d7", img: "images/iphone-15.jpeg" },
      { name: "Black", hex: "#343538", img: "images/iphone-15.jpeg" }
    ],
    display: "6.1\" Dynamic Island Display",
    chip: "A16 Bionic",
    camera: "48MP Dual Main Lens",
    battery: "Up to 20 hrs video",
    storageOptions: [
      { size: "128GB", price: 11200 },
      { size: "256GB", price: 12900 },
      { size: "512GB", price: 15900 }
    ]
  },

  // --- IPHONE 14 SERIES ---
  {
    id: "14pm",
    name: "iPhone 14 Pro Max",
    gen: "14",
    colors: [
      { name: "Deep Purple", hex: "#4b4351", img: "images/iphone-14-pro Max.jpeg" },
      { name: "Gold", hex: "#f3e0c8", img: "images/iphone-14-pro Max copy.jpeg" },
      { name: "Space Black", hex: "#302e2f", img: "images/iphone-14-pro Max.jpeg" },
      { name: "Silver", hex: "#f0f2f2", img: "images/iphone-14-pro Max.jpeg" }
    ],
    display: "6.7\" Super Retina XDR",
    chip: "A16 Bionic",
    camera: "48MP Main Camera",
    battery: "Up to 29 hrs video",
    storageOptions: [
      { size: "128GB", price: 13900 },
      { size: "256GB", price: 15500 },
      { size: "512GB", price: 18900 }
    ]
  },
  {
    id: "14p",
    name: "iPhone 14 Pro",
    gen: "14",
    colors: [
      { name: "Deep Purple", hex: "#4b4351", img: "images/iphone-14-pro.jpeg" },
      { name: "Gold", hex: "#f3e0c8", img: "images/iphone-14-pro copy.jpeg" },
      { name: "Space Black", hex: "#302e2f", img: "images/iphone-14-pro.jpeg" },
      { name: "Silver", hex: "#f0f2f2", img: "images/iphone-14-pro.jpeg" }
    ],
    display: "6.1\" Super Retina XDR",
    chip: "A16 Bionic",
    camera: "48MP Main Camera",
    battery: "Up to 23 hrs video",
    storageOptions: [
      { size: "128GB", price: 12200 },
      { size: "256GB", price: 13800 }
    ]
  },
  {
    id: "14plus",
    name: "iPhone 14 Plus",
    gen: "14",
    colors: [
      { name: "Blue", hex: "#a0b4c8", img: "images/iphone-14-Plus.jpeg" },
      { name: "Purple", hex: "#e5d7f2", img: "images/iphone-14-Plus copy.jpeg" },
      { name: "Midnight", hex: "#1f252c", img: "images/iphone-14-Plus.jpeg" },
      { name: "Starlight", hex: "#faf6f2", img: "images/iphone-14-Plus.jpeg" },
      { name: "(PRODUCT)RED", hex: "#e30016", img: "images/iphone-14-Plus.jpeg" },
      { name: "Yellow", hex: "#ffe680", img: "images/iphone-14-Plus.jpeg" }
    ],
    display: "6.7\" Super Retina XDR",
    chip: "A15 Bionic",
    camera: "12MP Dual Camera",
    battery: "Up to 26 hrs video",
    storageOptions: [
      { size: "128GB", price: 10800 },
      { size: "256GB", price: 12200 }
    ]
  },
  {
    id: "14base",
    name: "iPhone 14",
    gen: "14",
    colors: [
      { name: "Blue", hex: "#a0b4c8", img: "images/iphone-14.jpeg" },
      { name: "Purple", hex: "#e5d7f2", img: "images/iphone-14 copy.jpeg" },
      { name: "Midnight", hex: "#1f252c", img: "images/iphone-14.jpeg" },
      { name: "Starlight", hex: "#faf6f2", img: "images/iphone-14.jpeg" },
      { name: "(PRODUCT)RED", hex: "#e30016", img: "images/iphone-14.jpeg" },
      { name: "Yellow", hex: "#ffe680", img: "images/iphone-14.jpeg" }
    ],
    display: "6.1\" Super Retina XDR",
    chip: "A15 Bionic",
    camera: "12MP Dual Camera",
    battery: "Up to 20 hrs video",
    storageOptions: [
      { size: "128GB", price: 9900 },
      { size: "256GB", price: 11500 }
    ]
  },

  // --- IPHONE 13 SERIES (CLEARANCE) ---
  {
    id: "13pm",
    name: "iPhone 13 Pro Max",
    gen: "13",
    colors: [
      { name: "Sierra Blue", hex: "#9bb5ce", img: "images/iPhone-13-Pro-Max.jpeg" },
      { name: "Graphite", hex: "#545351", img: "images/iPhone-13-Pro-Max copy.jpeg" },
      { name: "Gold", hex: "#fae7cf", img: "images/iPhone-13-Pro-Max.jpeg" },
      { name: "Silver", hex: "#f0f2f2", img: "images/iPhone-13-Pro-Max.jpeg" },
      { name: "Alpine Green", hex: "#576856", img: "images/iPhone-13-Pro-Max.jpeg" }
    ],
    display: "6.7\" Super Retina XDR ProMotion",
    chip: "A15 Bionic",
    camera: "12MP Triple Lens System",
    battery: "Up to 28 hrs video",
    storageOptions: [
      { size: "128GB", price: 10900, originalPrice: 12900 },
      { size: "256GB", price: 12200, originalPrice: 14500 }
    ]
  },
  {
    id: "13p",
    name: "iPhone 13 Pro",
    gen: "13",
    colors: [
      { name: "Sierra Blue", hex: "#9bb5ce", img: "images/iPhone-13-Pro.jpeg" },
      { name: "Graphite", hex: "#545351", img: "images/iPhone-13-Pro copy.jpeg" },
      { name: "Gold", hex: "#fae7cf", img: "images/iPhone-13-Pro.jpeg" },
      { name: "Silver", hex: "#f0f2f2", img: "images/iPhone-13-Pro.jpeg" },
      { name: "Alpine Green", hex: "#576856", img: "images/iPhone-13-Pro.jpeg" }
    ],
    display: "6.1\" Super Retina XDR ProMotion",
    chip: "A15 Bionic",
    camera: "12MP Triple Lens System",
    battery: "Up to 22 hrs video",
    storageOptions: [
      { size: "128GB", price: 9200, originalPrice: 10900 },
      { size: "256GB", price: 10500, originalPrice: 12500 }
    ]
  },
  {
    id: "13base",
    name: "iPhone 13",
    gen: "13",
    colors: [
      { name: "Pink", hex: "#fadcda", img: "images/iPhone-13.jpeg" },
      { name: "Blue", hex: "#437691", img: "images/iPhone-13 copy.jpeg" },
      { name: "Midnight", hex: "#1f252c", img: "images/iPhone-13.jpeg" },
      { name: "Starlight", hex: "#faf6f2", img: "images/iPhone-13.jpeg" },
      { name: "Green", hex: "#3c4d3b", img: "images/iPhone-13.jpeg" },
      { name: "(PRODUCT)RED", hex: "#c8102e", img: "images/iPhone-13.jpeg" }
    ],
    display: "6.1\" Super Retina XDR",
    chip: "A15 Bionic",
    camera: "12MP Advanced Dual Camera",
    battery: "Up to 19 hrs video",
    storageOptions: [
      { size: "128GB", price: 7900, originalPrice: 9500 },
      { size: "256GB", price: 8900, originalPrice: 10700 }
    ]
  },

  // --- IPHONE 12 SERIES (CLEARANCE) ---
  {
    id: "12pm",
    name: "iPhone 12 Pro Max",
    gen: "12",
    colors: [
      { name: "Pacific Blue", hex: "#27547d", img: "images/iPhone-12-Pro-Max.jpeg" },
      { name: "Graphite", hex: "#4a494e", img: "images/iPhone-12-Pro-Max copy.jpeg" },
      { name: "Gold", hex: "#fae7cf", img: "images/iPhone-12-Pro-Max.jpeg" },
      { name: "Silver", hex: "#f0f2f2", img: "images/iPhone-12-Pro-Max.jpeg" }
    ],
    display: "6.7\" Super Retina XDR",
    chip: "A14 Bionic",
    camera: "12MP Pro Camera System",
    battery: "Up to 20 hrs video",
    storageOptions: [
      { size: "128GB", price: 8200, originalPrice: 9900 },
      { size: "256GB", price: 9400, originalPrice: 11200 }
    ]
  },
  {
    id: "12p",
    name: "iPhone 12 Pro",
    gen: "12",
    colors: [
      { name: "Pacific Blue", hex: "#27547d", img: "images/iPhone-12-Pro.jpeg" },
      { name: "Graphite", hex: "#4a494e", img: "images/iPhone-12-Pro copy.jpeg" },
      { name: "Gold", hex: "#fae7cf", img: "images/iPhone-12-Pro.jpeg" },
      { name: "Silver", hex: "#f0f2f2", img: "images/iPhone-12-Pro.jpeg" }
    ],
    display: "6.1\" Super Retina XDR",
    chip: "A14 Bionic",
    camera: "12MP Pro Camera System",
    battery: "Up to 17 hrs video",
    storageOptions: [
      { size: "128GB", price: 7100, originalPrice: 8500 },
      { size: "256GB", price: 8100, originalPrice: 9700 }
    ]
  },
  {
    id: "12base",
    name: "iPhone 12",
    gen: "12",
    colors: [
      { name: "Blue", hex: "#27547d", img: "images/iPhone-12.jpeg" },
      { name: "Green", hex: "#b5e3d8", img: "images/iPhone-12 copy.jpeg" },
      { name: "Black", hex: "#1f2022", img: "images/iPhone-12.jpeg" },
      { name: "White", hex: "#f9f6ef", img: "images/iPhone-12.jpeg" },
      { name: "Purple", hex: "#d1c4e9", img: "images/iPhone-12.jpeg" },
      { name: "(PRODUCT)RED", hex: "#d02a3a", img: "images/iPhone-12.jpeg" }
    ],
    display: "6.1\" Super Retina XDR OLED",
    chip: "A14 Bionic",
    camera: "12MP Dual System",
    battery: "Up to 17 hrs video",
    storageOptions: [
      { size: "64GB", price: 5800, originalPrice: 7000 },
      { size: "128GB", price: 6500, originalPrice: 7800 }
    ]
  },

  // --- IPHONE 11 SERIES (CLEARANCE) ---
  {
    id: "11pm",
    name: "iPhone 11 Pro Max",
    gen: "11",
    colors: [
      { name: "Midnight Green", hex: "#505e4d", img: "images/iphone-11-pro-Max.jpeg" },
      { name: "Space Gray", hex: "#4a494e", img: "images/iphone-11-pro-Max copy.jpeg" },
      { name: "Gold", hex: "#fae7cf", img: "images/iphone-11-pro-Max.jpeg" },
      { name: "Silver", hex: "#f0f2f2", img: "images/iphone-11-pro-Max.jpeg" }
    ],
    display: "6.5\" Super Retina XDR",
    chip: "A13 Bionic",
    camera: "12MP Triple Camera System",
    battery: "Up to 20 hrs video",
    storageOptions: [
      { size: "64GB", price: 6200, originalPrice: 7500 },
      { size: "256GB", price: 7100, originalPrice: 8500 }
    ]
  },
  {
    id: "11p",
    name: "iPhone 11 Pro",
    gen: "11",
    colors: [
      { name: "Midnight Green", hex: "#505e4d", img: "images/iphone-11-pro.jpeg" },
      { name: "Space Gray", hex: "#4a494e", img: "images/iphone-11-pro copy.jpeg" },
      { name: "Gold", hex: "#fae7cf", img: "images/iphone-11-pro.jpeg" },
      { name: "Silver", hex: "#f0f2f2", img: "images/iphone-11-pro.jpeg" }
    ],
    display: "5.8\" Super Retina XDR",
    chip: "A13 Bionic",
    camera: "12MP Triple Camera System",
    battery: "Up to 18 hrs video",
    storageOptions: [
      { size: "64GB", price: 5400, originalPrice: 6500 },
      { size: "256GB", price: 6200, originalPrice: 7500 }
    ]
  },
  {
    id: "11base",
    name: "iPhone 11",
    gen: "11",
    colors: [
      { name: "Purple", hex: "#d1c4e9", img: "images/iphone-11.jpeg" },
      { name: "Green", hex: "#b5e3d8", img: "images/iphone-11 copy.jpeg" },
      { name: "Yellow", hex: "#ffe680", img: "images/iphone-11.jpeg" },
      { name: "Black", hex: "#1f2022", img: "images/iphone-11.jpeg" },
      { name: "White", hex: "#f9f6ef", img: "images/iphone-11.jpeg" },
      { name: "(PRODUCT)RED", hex: "#d02a3a", img: "images/iphone-11.jpeg" }
    ],
    display: "6.1\" Liquid Retina HD",
    chip: "A13 Bionic",
    camera: "12MP Ultra Wide Dual Camera",
    battery: "Up to 17 hrs video",
    storageOptions: [
      { size: "64GB", price: 4600, originalPrice: 5600 },
      { size: "128GB", price: 5300, originalPrice: 6400 }
    ]
  },

  // --- IPHONE X / XS / XR SERIES (CLEARANCE) ---
  {
    id: "xsmax",
    name: "iPhone XS Max",
    gen: "x",
    colors: [
      { name: "Gold", hex: "#f1e3d3", img: "images/iPhone-Xs.jpeg" },
      { name: "Space Gray", hex: "#4a494e", img: "images/iPhone-Xs copy.jpeg" },
      { name: "Silver", hex: "#e2e4e5", img: "images/iPhone-Xs.jpeg" }
    ],
    display: "6.5\" Super Retina OLED",
    chip: "A12 Bionic",
    camera: "12MP Dual Camera System",
    battery: "Up to 15 hrs video",
    storageOptions: [
      { size: "64GB", price: 4800, originalPrice: 5800 },
      { size: "256GB", price: 5500, originalPrice: 6600 }
    ]
  },
  {
    id: "xs",
    name: "iPhone XS",
    gen: "x",
    colors: [
      { name: "Gold", hex: "#f1e3d3", img: "images/iPhone-Xs.jpeg" },
      { name: "Space Gray", hex: "#4a494e", img: "images/iPhone-Xs copy.jpeg" },
      { name: "Silver", hex: "#e2e4e5", img: "images/iPhone-Xs.jpeg" }
    ],
    display: "5.8\" Super Retina OLED",
    chip: "A12 Bionic",
    camera: "12MP Dual Camera System",
    battery: "Up to 14 hrs video",
    storageOptions: [
      { size: "64GB", price: 4200, originalPrice: 5100 },
      { size: "256GB", price: 4900, originalPrice: 5900 }
    ]
  },
  {
    id: "xr",
    name: "iPhone XR",
    gen: "x",
    colors: [
      { name: "Coral", hex: "#ff6b52", img: "images/iPhone-xr.jpeg" },
      { name: "Black", hex: "#1f2022", img: "images/iPhone-xr copy.jpeg" },
      { name: "Blue", hex: "#437691", img: "images/iPhone-xr.jpeg" },
      { name: "Yellow", hex: "#ffe680", img: "images/iPhone-xr.jpeg" },
      { name: "White", hex: "#f9f6ef", img: "images/iPhone-xr.jpeg" },
      { name: "(PRODUCT)RED", hex: "#d02a3a", img: "images/iPhone-xr.jpeg" }
    ],
    display: "6.1\" Liquid Retina HD",
    chip: "A12 Bionic",
    camera: "12MP Single Lens Camera",
    battery: "Up to 16 hrs video",
    storageOptions: [
      { size: "64GB", price: 3800, originalPrice: 4600 },
      { size: "128GB", price: 4400, originalPrice: 5300 }
    ]
  },
  {
    id: "xbase",
    name: "iPhone X",
    gen: "x",
    colors: [
      { name: "Silver", hex: "#e2e4e5", img: "images/iphone-x.jpeg" },
      { name: "Space Gray", hex: "#4a494e", img: "images/iphone-x copy.jpeg" }
    ],
    display: "5.8\" Super Retina OLED",
    chip: "A11 Bionic",
    camera: "12MP Dual Optical Zoom",
    battery: "Up to 13 hrs video",
    storageOptions: [
      { size: "64GB", price: 3400, originalPrice: 4100 },
      { size: "256GB", price: 3900, originalPrice: 4700 }
    ]
  }
];

// Inline SVG shown if a product photo fails to load (e.g. wrong filename/path
// on your server). No network request involved, so it can never itself fail.
const IMG_FALLBACK = 'data:image/svg+xml;utf8,' + encodeURIComponent(
  `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200">
     <rect width="200" height="200" fill="#f5f2ea"/>
     <rect x="70" y="40" width="60" height="120" rx="10" fill="none" stroke="#cba135" stroke-width="3"/>
     <circle cx="100" cy="145" r="4" fill="#cba135"/>
     <text x="100" y="185" font-family="monospace" font-size="11" fill="#a09a8c" text-anchor="middle">image not found</text>
   </svg>`
);

// Note on colour accuracy: several catalog colours share the same base
// photo (no separate photography exists per finish), so we deliberately
// don't tint the photo to "fake" a colour — a CSS filter can't reliably
// match Apple's real anodised/titanium finishes. The colour swatch's hex
// value next to each photo is the accurate source of truth for the finish.

const selectedStorageState = {};
const selectedColorState = {};
// Exposed for app.js (interactive viewer needs the currently selected color/image)
window.selectedStorageState = selectedStorageState;
window.selectedColorState = selectedColorState;

// ---------- State ----------
let currentGenFilter = 'all';
let currentSearchQuery = '';
let currentSort = 'featured';
let wishlistOnlyActive = false;

const genOrder = { '17': 7, '16': 6, '15': 5, '14': 4, '13': 3, '12': 2, '11': 1, 'x': 0 };

const lightHexList = ['#f9f6ef', '#f3f2ee', '#f0f2f2', '#faf6f2', '#f8f6f0', '#f2f3f5', '#ebebe7', '#e3e3e3', '#c4dbe0', '#b8c4b8', '#e8cfd8', '#f4f4f4', '#ffe680', '#f9f9f9', '#fadcda', '#b5e3d8', '#e5d7f2', '#e2e4e5', '#f1e3d3', '#fae7cf', '#d3e0d7', '#c9d8e0'];

function cleanId(str) {
  return str.replace(/[^a-zA-Z0-9]/g, '-');
}

// ---------- Wishlist (persisted in localStorage) ----------
function getWishlist() {
  try {
    const raw = localStorage.getItem('wishlist');
    return raw ? JSON.parse(raw) : [];
  } catch (e) {
    return [];
  }
}

function saveWishlist(list) {
  try {
    localStorage.setItem('wishlist', JSON.stringify(list));
  } catch (e) { /* storage unavailable, fail silently */ }
}

function isWishlisted(itemId) {
  return getWishlist().includes(itemId);
}

function toggleWishlist(itemId, btnEl) {
  let list = getWishlist();
  if (list.includes(itemId)) {
    list = list.filter(id => id !== itemId);
  } else {
    list.push(itemId);
  }
  saveWishlist(list);
  updateWishlistNavUI();

  if (btnEl) {
    btnEl.classList.toggle('active', list.includes(itemId));
  }

  // If the wishlist-only view is active and an item was removed, re-render
  if (wishlistOnlyActive && !list.includes(itemId)) {
    applyFilters();
  }
}

function updateWishlistNavUI() {
  const count = getWishlist().length;
  const countEl = document.getElementById('wishlistCount');
  if (countEl) {
    countEl.textContent = String(count);
    countEl.classList.toggle('hidden', count === 0);
    countEl.classList.toggle('flex', count > 0);
  }
  const navBtn = document.getElementById('wishlistNavBtn');
  const navIcon = document.getElementById('wishlistNavIcon');
  if (navBtn) {
    if (wishlistOnlyActive) {
      navBtn.classList.add('border-[#cba135]', 'bg-[#cba135]/10', 'text-[#cba135]');
      navBtn.title = 'Showing wishlist only — click to show all iPhones';
    } else {
      navBtn.classList.remove('border-[#cba135]', 'bg-[#cba135]/10', 'text-[#cba135]');
      navBtn.title = 'View wishlist';
    }
  }
  if (navIcon) {
    navIcon.setAttribute('fill', wishlistOnlyActive ? 'currentColor' : 'none');
  }

  // Unmissable banner + one-click exit, so the catalog never looks "broken"
  // just because this filter got switched on.
  const banner = document.getElementById('wishlistModeBanner');
  if (banner) {
    banner.classList.toggle('hidden', !wishlistOnlyActive);
    banner.classList.toggle('flex', wishlistOnlyActive);
  }
}

function toggleWishlistOnly() {
  wishlistOnlyActive = !wishlistOnlyActive;
  updateWishlistNavUI();
  applyFilters();
}

// ---------- Filtering + Sorting ----------
function applyFilters() {
  const searchInput = document.getElementById('searchInput');
  const sortSelect = document.getElementById('sortSelect');
  currentSearchQuery = searchInput ? searchInput.value.trim().toLowerCase() : '';
  currentSort = sortSelect ? sortSelect.value : 'featured';

  let items = catalog.slice();

  if (currentGenFilter !== 'all') {
    items = items.filter(i => i.gen === currentGenFilter);
  }

  if (currentSearchQuery) {
    items = items.filter(i =>
      i.name.toLowerCase().includes(currentSearchQuery) ||
      i.chip.toLowerCase().includes(currentSearchQuery) ||
      i.gen.toLowerCase().includes(currentSearchQuery)
    );
  }

  if (wishlistOnlyActive) {
    const wishlist = getWishlist();
    items = items.filter(i => wishlist.includes(i.id));
  }

  items = sortItems(items, currentSort);

  renderCatalog(items);
  updateResultCount(items.length);
}

function sortItems(items, sortMode) {
  const withBasePrice = items.map(i => ({
    item: i,
    basePrice: (selectedStorageState[i.id] || i.storageOptions[0]).price
  }));

  switch (sortMode) {
    case 'price-low':
      withBasePrice.sort((a, b) => a.basePrice - b.basePrice);
      break;
    case 'price-high':
      withBasePrice.sort((a, b) => b.basePrice - a.basePrice);
      break;
    case 'name':
      withBasePrice.sort((a, b) => a.item.name.localeCompare(b.item.name));
      break;
    case 'newest':
      withBasePrice.sort((a, b) => genOrder[b.item.gen] - genOrder[a.item.gen]);
      break;
    case 'featured':
    default:
      // Keep catalog's natural order (already newest-first by design)
      break;
  }

  return withBasePrice.map(w => w.item);
}

function updateResultCount(count) {
  const el = document.getElementById('resultCount');
  if (!el) return;
  if (wishlistOnlyActive) {
    el.textContent = `${count} model${count === 1 ? '' : 's'} in your wishlist`;
  } else if (currentGenFilter !== 'all' || currentSearchQuery) {
    el.textContent = `${count} model${count === 1 ? '' : 's'} found`;
  } else {
    el.textContent = 'Showing all models';
  }
}

// ---------- Rendering ----------
function renderCatalog(items) {
  const productGrid = document.getElementById('productGrid');
  if (!productGrid) return;

  productGrid.innerHTML = "";

  if (!items || items.length === 0) {
    const message = wishlistOnlyActive
      ? "Your wishlist is empty. Tap the heart icon on any model to save it here."
      : "No iPhone models found matching your search.";
    productGrid.innerHTML = `<div class="col-span-full text-center text-[#a09a8c] py-16 text-sm">${message}</div>`;
    return;
  }

  items.forEach((item, index) => {
    if (!selectedStorageState[item.id]) selectedStorageState[item.id] = item.storageOptions[0];
    if (!selectedColorState[item.id]) selectedColorState[item.id] = item.colors[0];

    const activeStorage = selectedStorageState[item.id];
    const activeColor = selectedColorState[item.id];
    const laybyInstallment = Math.round(activeStorage.price / 6);
    const isNew = item.gen === '17';
    const isSale = !!activeStorage.originalPrice;
    const wishActive = isWishlisted(item.id);

    const storageButtonsHTML = item.storageOptions.map((opt, i) => {
      const isSelected = opt.size === activeStorage.size;
      return `<button data-active="${isSelected}" data-idx="${i}"
                onclick="updateStorage('${item.id}', '${opt.size}')"
                class="storage-btn-${item.id} relative z-10 flex-1 text-center text-[11px] px-2.5 py-1.5 rounded-md font-medium transition-colors duration-300 ${isSelected ? 'text-[#cba135] dark:text-[#16140f] font-semibold' : 'text-[#6b6558] dark:text-[#9c9891] hover:text-[#16140f] dark:hover:text-white'}">${opt.size}</button>`;
    }).join('');

    const colorSwatchesHTML = item.colors.map(colorObj => {
      const isSelected = colorObj.name === activeColor.name;
      const ringClass = isSelected
        ? "ring-2 ring-[#16140f] dark:ring-[#cba135] ring-offset-2 dark:ring-offset-[#131316] scale-110"
        : "hover:scale-105 border border-black/10 dark:border-white/20";
      const isLightHex = lightHexList.includes(colorObj.hex.toLowerCase());
      const checkMark = isSelected ? `<svg class="w-3 h-3 ${isLightHex ? 'text-black' : 'text-white'}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>` : "";

      return `
        <button id="swatch-${item.id}-${cleanId(colorObj.name)}"
                onclick="updateColor('${item.id}', '${colorObj.name}')"
                title="${colorObj.name}"
                class="swatch-btn-${item.id} w-6 h-6 rounded-full flex items-center justify-center transition-all duration-200 ${ringClass}"
                style="background-color: ${colorObj.hex}">
          ${checkMark}
        </button>
      `;
    }).join('');

    const waMessage = encodeURIComponent(`Hi Latest-iPhone, I would like to inquire about the ${item.name} (${activeColor.name}, ${activeStorage.size}) priced at R ${activeStorage.price.toLocaleString()}.`);
    const encodedImgSrc = encodeURI(activeColor.img);

    const badgeHTML = isNew
      ? `<span class="tag-new absolute top-4 left-4 z-10 text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider shadow-sm">New</span>`
      : (isSale ? `<span id="sale-badge-${item.id}" class="tag-sale absolute top-4 left-4 z-10 text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider shadow-sm">Sale</span>` : '');

    const stockInfo = typeof getStockInfo === 'function' ? getStockInfo(activeStorage) : { label: 'In Stock', dot: 'bg-emerald-500', text: 'text-emerald-700 dark:text-emerald-400' };
    const rating = typeof getAverageRating === 'function' ? getAverageRating(item.id) : null;
    const isComparing = typeof compareList !== 'undefined' && compareList.includes(item.id);

    const savingsHTML = isSale
      ? `<div id="savings-${item.id}" class="text-[10px] font-mono text-[#b3272d] font-semibold">Save R ${(activeStorage.originalPrice - activeStorage.price).toLocaleString()}</div>`
      : `<div id="savings-${item.id}"></div>`;

    const originalPriceHTML = isSale
      ? `<span id="original-price-${item.id}" class="text-xs text-[#a09a8c] line-through font-mono ml-1.5">R ${activeStorage.originalPrice.toLocaleString()}</span>`
      : `<span id="original-price-${item.id}"></span>`;

    const card = `
      <div class="card-enter bg-white dark:bg-[#131316] border border-[#e5e1d8] dark:border-[#232019] rounded-2xl overflow-hidden hover:shadow-xl dark:hover:shadow-black/50 hover:border-[#cba135]/60 transition-all duration-300 flex flex-col justify-between group" style="animation-delay:${Math.min(index * 30, 300)}ms">
        <div>
          <div class="h-60 bg-[#fbfaf7] dark:bg-[#0b0b0d] p-6 flex items-center justify-center relative border-b border-[#f0ede4] dark:border-[#1c1c1f]">
            ${badgeHTML}
            <span class="absolute top-4 right-4 z-10 bg-[#f5f2ea] dark:bg-[#1a1a1d] text-[#6b6558] dark:text-[#9c9891] text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider shadow-xs">
              Series ${item.gen.toUpperCase()}
            </span>
            <button onclick="toggleWishlist('${item.id}', this)" title="Save to wishlist" class="heart-btn ${wishActive ? 'active' : ''} absolute bottom-3 right-3 z-10 w-8 h-8 rounded-full bg-white/90 dark:bg-[#131316]/90 border border-[#e5e1d8] dark:border-[#2a2620] flex items-center justify-center hover:scale-110 transition">
              <svg class="w-4 h-4 ${wishActive ? '' : 'text-[#6b6558] dark:text-[#9c9891]'}" fill="${wishActive ? 'currentColor' : 'none'}" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 016.364 0L12 7.636l1.318-1.318a4.5 4.5 0 116.364 6.364L12 21.364l-7.682-7.682a4.5 4.5 0 010-6.364z" />
              </svg>
            </button>
            <img id="img-${item.id}" src="${encodedImgSrc}" alt="${item.name}"
                 class="h-48 w-full object-contain drop-shadow-sm transition-all duration-300 group-hover:scale-105"
                 onerror="this.onerror=null; this.src='${IMG_FALLBACK}';">
          </div>

          <div class="p-6">
            <div class="flex items-center justify-between mb-1">
              <h3 class="font-display text-lg font-bold tracking-tight">${item.name}</h3>
              <button onclick="openViewer('${item.id}')" title="Interactive view" class="text-[10px] font-mono uppercase tracking-wide text-[#a87c1f] dark:text-[#cba135] hover:underline flex items-center gap-1 flex-shrink-0">
                <i data-lucide="eye" class="w-3 h-3"></i> View
              </button>
            </div>

            <div class="flex items-center justify-between mb-3">
              <button onclick="openReviewsModal('${item.id}')" class="flex items-center gap-1 hover:opacity-80">
                ${rating ? `<div class="flex">${starsHTML(rating.avg, 'w-3 h-3')}</div><span class="text-[10px] font-mono text-[#a09a8c]">(${rating.count})</span>` : `<span class="text-[10px] font-mono text-[#a09a8c] underline">Write a review</span>`}
              </button>
              <span class="flex items-center gap-1 text-[10px]">
                <span data-stock-dot class="w-1.5 h-1.5 rounded-full ${stockInfo.dot}"></span>
                <span data-stock-label class="font-mono ${stockInfo.text}">${stockInfo.label}</span>
              </span>
            </div>

            <div class="mb-4">
              <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] uppercase font-semibold text-[#a09a8c] tracking-wider">Color</span>
                <span id="color-name-${item.id}" class="text-xs font-medium text-[#6b6558] dark:text-[#9c9891]">${activeColor.name}</span>
              </div>
              <div class="flex flex-wrap items-center gap-2">
                ${colorSwatchesHTML}
              </div>
              <p class="mt-1.5 text-[9px] text-[#a09a8c] leading-snug">Swatch is the accurate finish colour; photo is a representative angle.</p>
            </div>

            <div class="mb-4">
              <span class="text-[10px] uppercase font-semibold text-[#a09a8c] tracking-wider block mb-1.5">Storage Capacity</span>
              <div id="storage-track-${item.id}" class="relative flex gap-1 bg-[#f5f2ea] dark:bg-[#1a1a1d] rounded-lg p-1">
                <div id="storage-pill-${item.id}" class="absolute top-1 bottom-1 rounded-md bg-[#16140f] dark:bg-[#cba135] transition-all duration-300 ease-out" style="left:0px;width:0px;"></div>
                ${storageButtonsHTML}
              </div>
            </div>

            <div class="bg-[#f5f2ea] dark:bg-[#1a1a1d] p-3 rounded-xl flex items-center justify-between mb-4">
              <div>
                <div class="flex items-baseline">
                  <span id="price-${item.id}" class="text-xl font-bold font-mono tracking-tight">R ${activeStorage.price.toLocaleString()}</span>
                  ${originalPriceHTML}
                </div>
                ${savingsHTML}
              </div>
              <div class="text-right">
                <div id="layby-${item.id}" class="text-xs font-semibold font-mono text-[#a87c1f] dark:text-[#cba135]">R ${laybyInstallment.toLocaleString()}/mo</div>
                <div class="text-[10px] text-[#a09a8c] font-medium">6 Months Lay-by</div>
              </div>
            </div>

            <div class="space-y-2 border-t border-[#f0ede4] dark:border-[#1c1c1f] pt-4">
              <div class="flex items-center text-xs text-[#6b6558] dark:text-[#9c9891]">
                <i data-lucide="monitor" class="w-3.5 h-3.5 text-[#a09a8c] mr-2 flex-shrink-0"></i>
                <span class="truncate">${item.display}</span>
              </div>
              <div class="flex items-center text-xs text-[#6b6558] dark:text-[#9c9891]">
                <i data-lucide="cpu" class="w-3.5 h-3.5 text-[#a09a8c] mr-2 flex-shrink-0"></i>
                <span class="truncate">${item.chip}</span>
              </div>
              <div class="flex items-center text-xs text-[#6b6558] dark:text-[#9c9891]">
                <i data-lucide="camera" class="w-3.5 h-3.5 text-[#a09a8c] mr-2 flex-shrink-0"></i>
                <span class="truncate">${item.camera}</span>
              </div>
              <div class="flex items-center text-xs text-[#6b6558] dark:text-[#9c9891]">
                <i data-lucide="battery" class="w-3.5 h-3.5 text-[#a09a8c] mr-2 flex-shrink-0"></i>
                <span class="truncate">${item.battery}</span>
              </div>
            </div>
          </div>
        </div>

        <div class="p-6 pt-0">
          <label class="flex items-center gap-2 mb-3 text-[11px] text-[#6b6558] dark:text-[#9c9891] cursor-pointer select-none">
            <input type="checkbox" onchange="toggleCompare('${item.id}', this)" ${isComparing ? 'checked' : ''} class="w-3.5 h-3.5 rounded accent-[#cba135]">
            Add to comparison
          </label>
          <div class="grid grid-cols-2 gap-2">
            <button onclick="addToCart('${item.id}')"
                    class="w-full bg-[#16140f] dark:bg-[#cba135] hover:opacity-90 text-[#cba135] dark:text-[#16140f] text-xs font-semibold py-2.5 rounded-xl flex items-center justify-center space-x-1.5 transition shadow-sm">
              <i data-lucide="shopping-bag" class="w-3.5 h-3.5"></i>
              <span>Add to Cart</span>
            </button>
            <a id="wa-link-${item.id}" href="https://wa.me/${waPhone}?text=${waMessage}" target="_blank"
               class="w-full border border-[#e5e1d8] dark:border-[#2a2620] hover:border-[#cba135] text-[#16140f] dark:text-[#f5f1e8] text-xs font-semibold py-2.5 rounded-xl flex items-center justify-center space-x-1.5 transition">
              <i data-lucide="message-circle" class="w-3.5 h-3.5"></i>
              <span>Ask</span>
            </a>
          </div>
        </div>
      </div>
    `;
    productGrid.insertAdjacentHTML('beforeend', card);
  });

  if (window.lucide && typeof window.lucide.createIcons === 'function') {
    window.lucide.createIcons();
  }

  // Position each storage pill once the browser has laid out the buttons.
  // Skipping the transition on this first placement avoids a "slide in from
  // the left" flash on every re-render.
  requestAnimationFrame(() => {
    items.forEach(item => positionStoragePill(item.id, false));
  });
}

// Moves the dark "selected storage" pill to sit exactly behind the active
// button, measured in real pixels so it always lines up regardless of how
// many storage tiers a given model has. animate=false skips the transition
// (used on first render so the pill doesn't visibly slide in from 0).
function positionStoragePill(itemId, animate) {
  const track = document.getElementById(`storage-track-${itemId}`);
  const pill = document.getElementById(`storage-pill-${itemId}`);
  if (!track || !pill) return;

  const activeBtn = track.querySelector(`.storage-btn-${itemId}[data-active="true"]`);
  if (!activeBtn) return;

  const trackRect = track.getBoundingClientRect();
  const btnRect = activeBtn.getBoundingClientRect();
  const left = btnRect.left - trackRect.left;
  const width = btnRect.width;

  if (!animate) pill.style.transition = 'none';
  pill.style.left = `${left}px`;
  pill.style.width = `${width}px`;
  if (!animate) {
    void pill.offsetWidth; // force reflow so the transition removal takes effect
    pill.style.transition = '';
  }
}
window.addEventListener('resize', () => {
  document.querySelectorAll('[id^="storage-track-"]').forEach(track => {
    const itemId = track.id.replace('storage-track-', '');
    positionStoragePill(itemId, false);
  });
});

// ---------- Color & Storage selection ----------
function updateColor(itemId, colorName) {
  const item = catalog.find(i => i.id === itemId);
  if (!item) return;

  const selectedColor = item.colors.find(c => c.name === colorName);
  if (!selectedColor) return;

  selectedColorState[itemId] = selectedColor;

  const colorNameLabel = document.getElementById(`color-name-${itemId}`);
  if (colorNameLabel) colorNameLabel.innerText = selectedColor.name;

  const imgElement = document.getElementById(`img-${itemId}`);
  if (imgElement && selectedColor.img) {
    imgElement.onerror = function () { this.onerror = null; this.src = IMG_FALLBACK; };
    imgElement.src = encodeURI(selectedColor.img);
  }

  const activeStorage = selectedStorageState[itemId];
  const waLink = document.getElementById(`wa-link-${itemId}`);
  if (waLink && activeStorage) {
    const waMessage = encodeURIComponent(`Hi Latest-iPhone, I would like to inquire about the ${item.name} (${selectedColor.name}, ${activeStorage.size}) priced at R ${activeStorage.price.toLocaleString()}.`);
    waLink.href = `https://wa.me/${waPhone}?text=${waMessage}`;
  }

  const swatches = document.querySelectorAll(`.swatch-btn-${itemId}`);
  swatches.forEach(btn => {
    btn.className = `swatch-btn-${itemId} w-6 h-6 rounded-full flex items-center justify-center transition-all duration-200 hover:scale-105 border border-black/10 dark:border-white/20`;
    btn.innerHTML = '';
  });

  const activeBtn = document.getElementById(`swatch-${itemId}-${cleanId(colorName)}`);
  if (activeBtn) {
    activeBtn.className = `swatch-btn-${itemId} w-6 h-6 rounded-full flex items-center justify-center transition-all duration-200 ring-2 ring-[#16140f] dark:ring-[#cba135] ring-offset-2 dark:ring-offset-[#131316] scale-110`;
    const isLightHex = lightHexList.includes(selectedColor.hex.toLowerCase());
    activeBtn.innerHTML = `<svg class="w-3 h-3 ${isLightHex ? 'text-black' : 'text-white'}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>`;
  }
}

function updateStorage(itemId, storageSize) {
  const item = catalog.find(i => i.id === itemId);
  if (!item) return;

  const selectedOpt = item.storageOptions.find(o => o.size === storageSize);
  if (!selectedOpt) return;

  selectedStorageState[itemId] = selectedOpt;

  // Move the selection pill and flip each button's text colour so the
  // highlighted storage size always matches what's actually selected.
  const track = document.getElementById(`storage-track-${itemId}`);
  if (track) {
    track.querySelectorAll(`.storage-btn-${itemId}`).forEach(btn => {
      const isSelected = btn.textContent.trim() === storageSize;
      btn.dataset.active = isSelected ? 'true' : 'false';
      btn.className = `storage-btn-${itemId} relative z-10 flex-1 text-center text-[11px] px-2.5 py-1.5 rounded-md font-medium transition-colors duration-300 ${isSelected ? 'text-[#cba135] dark:text-[#16140f] font-semibold' : 'text-[#6b6558] dark:text-[#9c9891] hover:text-[#16140f] dark:hover:text-white'}`;
    });
  }
  positionStoragePill(itemId, true);

  const priceElement = document.getElementById(`price-${itemId}`);
  const laybyElement = document.getElementById(`layby-${itemId}`);
  if (priceElement) priceElement.innerText = `R ${selectedOpt.price.toLocaleString()}`;
  if (laybyElement) laybyElement.innerText = `R ${Math.round(selectedOpt.price / 6).toLocaleString()}/mo`;

  const savingsEl = document.getElementById(`savings-${itemId}`);
  const originalPriceEl = document.getElementById(`original-price-${itemId}`);
  const saleBadge = document.getElementById(`sale-badge-${itemId}`);
  if (selectedOpt.originalPrice) {
    if (savingsEl) savingsEl.innerHTML = `Save R ${(selectedOpt.originalPrice - selectedOpt.price).toLocaleString()}`;
    if (originalPriceEl) originalPriceEl.innerHTML = `R ${selectedOpt.originalPrice.toLocaleString()}`;
    if (saleBadge) saleBadge.classList.remove('hidden');
  } else {
    if (savingsEl) savingsEl.innerHTML = '';
    if (originalPriceEl) originalPriceEl.innerHTML = '';
    if (saleBadge) saleBadge.classList.add('hidden');
  }

  const activeColor = selectedColorState[itemId];
  const waLink = document.getElementById(`wa-link-${itemId}`);
  if (waLink && activeColor) {
    const waMessage = encodeURIComponent(`Hi Latest-iPhone, I would like to inquire about the ${item.name} (${activeColor.name}, ${selectedOpt.size}) priced at R ${selectedOpt.price.toLocaleString()}.`);
    waLink.href = `https://wa.me/${waPhone}?text=${waMessage}`;
  }

  // Stock label depends on the selected storage tier
  if (typeof getStockInfo === 'function') {
    const stockInfo = getStockInfo(selectedOpt);
    const card = priceElement ? priceElement.closest('.card-enter') : null;
    if (card) {
      const stockLabelEl = card.querySelector('[data-stock-label]');
      const stockDotEl = card.querySelector('[data-stock-dot]');
      if (stockLabelEl) { stockLabelEl.textContent = stockInfo.label; stockLabelEl.className = `font-mono ${stockInfo.text}`; }
      if (stockDotEl) { stockDotEl.className = `w-1.5 h-1.5 rounded-full ${stockInfo.dot}`; }
    }
  }

  // Re-sort if the active sort mode depends on price
  if (currentSort === 'price-low' || currentSort === 'price-high') {
    applyFilters();
  }
}

// ---------- Generation filter ----------
function filterGen(gen, btnElement) {
  currentGenFilter = gen;

  const buttons = document.querySelectorAll('.gen-btn');
  buttons.forEach(btn => {
    btn.classList.remove('bg-[#16140f]', 'text-[#cba135]', 'dark:bg-[#cba135]', 'dark:text-[#16140f]');
    btn.classList.add('bg-white', 'text-[#6b6558]', 'dark:bg-[#131316]', 'dark:text-[#9c9891]');
  });

  if (btnElement) {
    btnElement.classList.remove('bg-white', 'text-[#6b6558]', 'dark:bg-[#131316]', 'dark:text-[#9c9891]');
    btnElement.classList.add('bg-[#16140f]', 'text-[#cba135]', 'dark:bg-[#cba135]', 'dark:text-[#16140f]');
  }

  applyFilters();
}

// Kept for compatibility; delegates to the shared filter pipeline.
function filterCatalog() {
  applyFilters();
}

// ---------- Initializer ----------
function initCatalog() {
  updateWishlistNavUI();
  renderCatalog(catalog);
  updateResultCount(catalog.length);
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initCatalog);
} else {
  initCatalog();
}