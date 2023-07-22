(function () {
    type DataBaseCons = Array<string|number>;
    const database: DataBaseCons = ['Hello', 14, 'strong'];

    for (const i of database) {
        console.log(typeof i);
    }
})();