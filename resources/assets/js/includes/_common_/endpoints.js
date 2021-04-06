import axios from 'axios';
import createAuthRefreshInterceptor from '~/includes/expired-session-refetch';

createAuthRefreshInterceptor(axios);

export const currentUser = params => axios.get(zRoute('users.current'), params);
