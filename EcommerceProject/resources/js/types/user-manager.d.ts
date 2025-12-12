interface UserInfo {
    id: number;
    email: string;
    username: string;
    name: string;
    first_name: string;
    last_name: string;
    birthday: string | null;
    avatar: string | null;
    role: 'admin' | 'user';
    created_at: string;
    email_verified_at: string | null;
}

interface UserManager {
    initializeUserData(): Promise<UserInfo | null>;
    clearUserCache(): void;
    refreshUserData(): Promise<UserInfo | null>;
}

interface Window {
    secure_userInfo: UserInfo | null;
    userManager: UserManager;
}
