export default {
    state: {},
    mutations: {},
    getters: {},
    actions: {
        setCookie: (context, {key, value}) => {
            document.cookie = `${key}=${value}`;
        },
        getCookie: (context, key) => {
            let regex = `(?:(?:^|.*;\\s*)${key}\\s*\\=\\s*([^;]*).*$)`
            let value = document.cookie.replace(new RegExp(regex), "$1"); 
            
            if(value === 'true')
            {
                return true;
            }
            else if(value === 'false')
            {
                return false;
            }
            else
            {
                return value;
            }
        },
        checkCookie: (context, key) => {

        },
    },
}