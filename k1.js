const puppeteer = require('puppeteer');
const iPhone = puppeteer.devices['iPhone 6'];
const fs = require('fs');

(async () => {
  const browser = await puppeteer.launch();

  // Debugging
  // const browser = await puppeteer.launch({headless: false, slowMo: 250});

  const page = await browser.newPage();
  await page.emulate(iPhone);

  var links = [];

  for (let index = 0; index < 5; index++) {
    var link;
    if (index == 0) {
      link = 'https://krimvsem.ru/search';
    } else {
      link = 'https://krimvsem.ru/search/iPage,' + String(index);
    }
    
    await page.goto(link);

    // Cut links
    var dirtyLinks = await page.evaluate(_ => {
      let dirtyArray = document.querySelectorAll('.middle h3 a');
      let testArray = [];
      for (let i = 0; i < dirtyArray.length; i++) {
        testArray.push(dirtyArray[i].href);
      }
      return testArray;
    });
    links = links.concat(dirtyLinks);
  }

  // Array for items
  var result = [];

  for (let index = 0; index < links.length; index++) {
    let linkToItem = links[index];
    await page.goto(linkToItem);
    
    // ID helper
    var idRegex = /\i(\d{3})/g;
    var idFound = linkToItem.match(idRegex);
    if (idFound === null) {
      continue;
    }
    idFound[0] = idFound[0].replace('i', '');

    // New item
    var newItem = {
      id: idFound[0],

      title: await page.evaluate(_ => {
        return document.querySelector('#right h2').innerText;
      }),

      price: await page.evaluate(_ => {
        return document.querySelector('.price .ins .right').innerText;
      }),

      img: await page.evaluate(_ => {
        let img = document.querySelector('.item-bxslider li a img');
        if (img !== null) {
          return !img.src ? '' : img.src;
        } else {
          return '';
        }
      }),

      location: await page.evaluate(_ => {
        return document.querySelector('.loc-text .elem').innerText;
      }),

      phone: await page.evaluate(_ => {
        return document.querySelector('.phone-show').rel;
      }),

      desc: await page.evaluate(_ => {
        return document.querySelector('.item-description').content;
      }),

      user: await page.evaluate(_ => {
        return document.querySelector('.name a').innerText.trim();
      }),

      source: 'k1'
    };

    if (newItem.img == '') {
      continue;
    }

    if (newItem.location == '') {
      continue;
    }
    
    result.push(newItem);
  }

  // @TODO Add axios
  fs.writeFile('k1.json', JSON.stringify(result), () => {});
  await browser.close();
})();