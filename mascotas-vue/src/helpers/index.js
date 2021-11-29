import {PATH_IMG} from "@/constants";
import {PATH_STATIC_IMG} from "@/constants";

const formatDate = function(date) {
        if (!date) return null

        const [year, month, day] = date.split('-')
        return `${day}/${month}/${year}`
    
};

const createImgPath = function(path) {
    return PATH_IMG + path;
}

const createStaticImgPath = function(path) {
    return PATH_STATIC_IMG + path;
}

export {
    formatDate,
    createImgPath,
    createStaticImgPath,
}