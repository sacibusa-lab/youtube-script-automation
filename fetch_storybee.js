const puppeteer = require('puppeteer');

(async () => {
    try {
        const browser = await puppeteer.launch({ headless: 'new' });
        const page = await browser.newPage();
        await page.goto('https://storybeeai.com/', { waitUntil: 'networkidle0' });
        const text = await page.evaluate(() => {
            // Function to get text from all elements
            return document.body.innerText;
        });
        const html = await page.evaluate(() => {
            return document.body.innerHTML;
        });
        const fs = require('fs');
        fs.writeFileSync('storybee_content.txt', text);
        console.log('Successfully fetched content');
        await browser.close();
    } catch (e) {
        console.error(e);
    }
})();
