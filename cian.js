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

  for (let index = 0; index < 3 /* 30 */; index++) {
    var link = 'https://www.cian.ru/cat.php?deal_type=sale&engine_version=2&offer_type=flat&region=-1&room1=1&room2=1&room3=1&room4=1&room5=1&room6=1&room7=1&room9=1&p=' + String(index);
    
    await page.goto(link);

    // Cut links
    var dirtyLinks = await page.evaluate(_ => {
      let dirtyArray = document.querySelectorAll('#app div div div div div div div div div a');
      let testArray = [];
      for (let i = 0; i < dirtyArray.length; i++) {
        if (~dirtyArray[i].href.indexOf('/flat/')) {
          testArray.push(dirtyArray[i].href);
        }
      }
      return testArray;
    });
    links = links.concat(dirtyLinks);
  }

  // Array for items
  /*var result = [];

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
      }).catch((e) => console.log(e)),

      price: await page.evaluate(_ => {
        return document.querySelector('.price .ins .right').innerText;
      }).catch((e) => console.log(e)),

      img: await page.evaluate(_ => {
        let img = document.querySelector('.item-bxslider li a img');
        if (img !== null) {
          return !img.src ? '' : img.src;
        } else {
          return '';
        }
      }).catch((e) => console.log(e)),

      location: await page.evaluate(_ => {
        return document.querySelector('.loc-text .elem').innerText;
      }).catch((e) => console.log(e)),

      phone: await page.evaluate(_ => {
        return document.querySelector('.phone-show').rel;
      }).catch((e) => console.log(e)),

      desc: await page.evaluate(_ => {
        return document.querySelector('.item-description').content;
      }).catch((e) => console.log(e)),

      user: await page.evaluate(_ => {
        return document.querySelector('.name a').innerText.trim();
      }).catch((e) => console.log(e)),

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
*/
  // @TODO Add axios
  fs.writeFile('cian.json', JSON.stringify(links), () => {});
  await browser.close();
})();