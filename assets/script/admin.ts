// wp notification bell admin code class for main methods

class WpnbAdm {
    static slugify(str: string, exc: string = ''): string {
        return String(str)
            .normalize('NFKD')                  // split accented characters into their base characters and diacritical marks
            .replace(/[\u0300-\u036f]/g, '')    // remove all the accents, which happen to be all in the \u03xx UNICODE block.
            .trim()                             // trim leading or trailing whitespace
            .toLowerCase()                      // convert to lowercase
            .replace(new RegExp(`[^a-z0-9${exc} -]`, 'g'), '')        // remove non-alphanumeric characters
            .replace(/\s+/g, '-')               // replace spaces with hyphens
            .replace(/-+/g, '-');               // remove consecutive hyphens
    }

    static randomStr(length: number): string {
        let result              = '';
        const characters        = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        const charactersLength  = characters.length;
        let counter = 0;
        while (counter < length) {
            result += characters.charAt(Math.floor(Math.random() * charactersLength));
            counter += 1;
        }

        return result;
    }
}

export default WpnbAdm;