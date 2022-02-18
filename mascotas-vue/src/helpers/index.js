import {PATH_IMG} from "@/constants";
import {PATH_STATIC_IMG} from "@/constants";
import {URL_REQUESTS} from "@/constants";

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

const createRequestAcceptUrl = function (requestId) {
    return URL_REQUESTS + requestId + "/aceptar";
}

const nameToDisplay = function(user) {
    return user.public_name ? user.public_name : user.first_name + ' ' + user.last_name;
}

const getCurrentDate = function() {
    return (new Date(Date.now() - (new Date()).getTimezoneOffset() * 60000)).toISOString().substr(0, 10);
}

export {
    formatDate,
    createImgPath,
    createStaticImgPath,
    createRequestAcceptUrl,
    nameToDisplay,
    getCurrentDate,
}