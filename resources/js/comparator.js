export default class Comparator {
    constructor() {
    }

    compare(objectOne, objectTwo) {
        let truth = [];
        for (let key in objectOne) {
            if (objectOne.hasOwnProperty(key)) {
                let savedValue = objectOne[key];
                truth.push(objectTwo[key] === savedValue);
            }
        }

        return ($.inArray(false, truth) !== -1);

    }
}
