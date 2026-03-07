const puppeteer = require('puppeteer');
const fs = require('fs');

(async () => {
    try {
        const browser = await puppeteer.launch({ headless: 'new' });
        const page = await browser.newPage();
        await page.goto('https://storybeeai.com/', { waitUntil: 'networkidle0' });
        const text = await page.evaluate(() => document.body.innerText);
        fs.writeFileSync('storybee_content.txt', text);
        console.log('Successfully fetched content');
        await browser.close();
    } catch (e) {
        console.error(e);
        process.exit(1);
    }
})();
