import { encryptData, decryptData } from './crypto';

window.secure_userInfo = null;
window.userManager = {
    async initializeUserData() {
        try {
            const authToken = window.getCookie('auth_token', localStorage.getItem('auth_token'));

            if(!authToken) {
                window.secure_userInfo = null;
                return null;
            }

            const encryptedUserCache = window.getCookie('cipher_user');

            if(encryptedUserCache) {
                try {
                    const decryptedData = await decryptData(encryptedUserCache);
                    const userData = JSON.parse(decryptedData);

                    window.secure_userInfo = userData;
                    return userData;

                }catch(decryptError) {
                    window.setCookie('cipher_user', '', {
                        expires: new Date(),
                        path: '/'
                    });
                }
            }

            const { data } = await window.http.get('/me');

            if(data.user) {
                window.secure_userInfo = data.user;

                const encryptedUserData = await encryptData(JSON.stringify(data.user));
                window.setCookie('cipher_user', encryptedUserData, {
                    path: '/',
                    secure: true,
                    sameSite: 'Strict'
                });

                return data.user;

            }else {
                window.secure_userInfo = null;
                return null;
            }

        }catch(axiosError) {
            const message = axiosError.response?.data?.message ?? axiosError.message;
            console.error('Error initializing user data: ', message);
            window.secure_userInfo = null;

            return null;
        }
    },

    clearUserCache() {
        window.secure_userInfo = null;
        window.setCookie('cipher_user', '', {
            expires: new Date(),
            path: '/'
        });
    },

    async refreshUserData() {
        this.clearUserCache();
        return await this.initializeUserData();
    }
};

window.userManager.initializeUserData();
Object.freeze(window.userManager);
