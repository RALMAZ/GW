const puppeteer = require('puppeteer');
const iPhone = puppeteer.devices['iPhone 6'];
const fs = require('fs');

(async () => {
  const browser = await puppeteer.launch();

  // Debugging
  // const browser = await puppeteer.launch({headless: false, slowMo: 250});

  const page = await browser.newPage();
  await page.emulate(iPhone);
  await page.goto('https://www.avito.ru/rossiya/kvartiry');

  // Scroll page (for new ajax items)
  for (let index = 0; index < 50; index++) {
    await page.evaluate(_ => {
      window.scrollBy(0, window.innerHeight);
    });
  }

  // Cut links
  var content = await page.content();
  var regEx = /data-marker="item\/link" href="([\-\/\_\w\.]*)/g;
  var links = content.match(regEx);

  // Remove excess result from .match
  links['input'] = '';
  links.splice(links.length, 1);

  // Cleaning
  for (let index = 0; index < links.length; index++) {
    links[index] = links[index].replace('data-marker="item\/link" href="', '');
  }

  // Array for items
  var result = [];

  for (let index = 0; index < links.length; index++) {
    let linkToItem = 'https://www.avito.ru' + links[index];
    await page.goto(linkToItem);
    
    // ID helper
    var idRegex = /\d{10}/g;
    var idFound = linkToItem.match(idRegex);
    if (idFound === null) {
      continue;
    }

    // New item
    var newItem = {
      id: idFound[0],

      title: await page.evaluate(_ => {
        return document.querySelector('h1 span').innerText;
      }).catch((e) => console.log(e)),

      price: await page.evaluate(_ => {
        return document.querySelector('p span span').innerText;
      }).catch((e) => console.log(e)),

      img: await page.evaluate(_ => {
        let img = document.querySelector('ul li div div img').src;
        if (!img) {
          return '';
        } else {
          return img;
        }
      }).catch((e) => console.log(e)),

      location: await page.evaluate(_ => {
        return document.querySelector('button span').innerText;
      }).catch((e) => console.log(e)),

      phone: await page.evaluate(_ => {
        let phone = document.querySelector('div[data-marker="item-contact-bar"] div div a').href;
        return phone.replace('tel:', '');
      }).catch((e) => console.log(e)),

      desc: await page.evaluate(_ => {
        return document.querySelector('meta[itemprop="description"]').content;
      }).catch((e) => console.log(e)),

      user: await page.evaluate(_ => {
        return document.querySelector('div[data-marker="item-contact-bar"] div a div span').innerText;
      }).catch((e) => console.log(e)),

      source: 'avito'
    };

    if (newItem.img == '') {
      continue;
    }
    
    result.push(newItem);

    // Screenshots for testing
    // await page.screenshot({path: 'example' + index + '.png'});
  }
  
  // @TODO Add axios
  fs.writeFile('avito.json', JSON.stringify(result), () => {});
  await browser.close();
})();