import puppeteer from 'puppeteer';

(async () => {
    const browser = await puppeteer.launch();
    const page = await browser.newPage();

    page.on('console', msg => {
        if (msg.type() === 'error') {
            console.log('PAGE LOG ERROR:', msg.text());
        }
    });

    page.on('pageerror', error => {
        console.log('PAGE ERROR:', error.message);
    });

    await page.goto('http://127.0.0.1:8000/projects/6');

    const html = await page.content();
    console.log("HTML SNAPSHOT LENGTH:", html.length);
    const errors = await page.evaluate(() => {
        let errs = [];
        if (window.Alpine && window.Alpine.store) { errs.push('Alpine is loaded'); }
        return errs;
    });
    console.log("EVAL:", errors);

    await browser.close();
})();
