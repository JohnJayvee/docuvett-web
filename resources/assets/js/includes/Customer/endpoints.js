import axios from 'axios';
import createAuthRefreshInterceptor from '~/includes/expired-session-refetch';

createAuthRefreshInterceptor(axios);
