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

    var newItem = {};

    // ID
    var idRegex = /\d{10}/g;
    var idFound = linkToItem.match(idRegex);
    if (idFound === null) {
      continue;
    }
    newItem.id = idFound[0];

    // Title
    newItem.title = await page.evaluate(_ => {
      return document.querySelector('h1 span').innerHTML;
    });

    // Price
    newItem.price = await page.evaluate(_ => {
      return document.querySelector('p span span').innerHTML;
    });

    // Photo
    newItem.img = await page.evaluate(_ => {
      return document.querySelector('ul li div div img').src;
    });

    // Location
    newItem.location = await page.evaluate(_ => {
      return document.querySelector('button span').innerHTML;
    });

    // Phone
    newItem.phone = await page.evaluate(_ => {
      let phone = document.querySelector('div[data-marker="item-contact-bar"] div div a').href;
      return phone.replace('tel:', '');
    });

    // Description
    newItem.desc = await page.evaluate(_ => {
      return document.querySelector('meta[itemprop="description"]').content;
    });

    // Username
    newItem.user = await page.evaluate(_ => {
      return document.querySelector('div[data-marker="item-contact-bar"] div a div span').innerHTML;
    });
    
    result.push(newItem);

    // Screenshots for testing
    // await page.screenshot({path: 'example' + index + '.png'});
  }
  
  // @TODO Add axios
  // fs.writeFile('avito.json', JSON.stringify(result), () => {});
  await browser.close();
})();