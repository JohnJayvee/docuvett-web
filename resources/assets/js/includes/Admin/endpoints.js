import axios from 'axios';
import createAuthRefreshInterceptor from '~/includes/expired-session-refetch';

createAuthRefreshInterceptor(axios);

export const getUserList                  = (p, o) => axios.get      (zRoute('users.index', p), o);
export const getUserCompanyList           = params => axios.get      (zRoute('users.companies', { user: params.id }));
export const getUser                      = params => axios.get      (zRoute('users.read', { user: params.id }));
export const getUserActiveClients         = (p, o) => axios.get      (zRoute('users.active-clients', p), o);
export const addUser                      = params => axios.post     (zRoute('users.create'), params);
export const removeUser                   = id     => axios.delete   (zRoute('users.delete', { user: id }));
export const deleteUserAvatar             = params => axios.delete   (zRoute('users.delete-avatar'), {params});
export const editUser                     = params => axios.put      (zRoute('users.update', { user: params.get('id') }), params);
export const exportUser                   = params => axios.get      (zRoute('users.export', params), { responseType: 'blob' });
export const importUser                   = params => axios.post     (zRoute('users.import'), params);
export const addUserActiveClient          = params => axios.post     (zRoute('users.add-active-clients'), params);
export const removeUserActiveClient       = params => axios.post     (zRoute('users.delete-active-clients'), params);

export const getRoleList                  = (p, o) => axios.get      (zRoute('roles.index', p), o);
export const getRole                      = params => axios.get      (zRoute('roles.read', { role: params.id }));
export const getRoleAutocomplete          = params => axios.get      (zRoute('roles.autocomplete', params));
export const addRole                      = params => axios.post     (zRoute('roles.create'), params);
export const removeRole                   = id     => axios.delete   (zRoute('roles.delete', { role: id }));
export const editRole                     = params => axios.put      (zRoute('roles.update', { role: params.id }), params);

export const getPermissionList            = params => axios.get      (zRoute('permissions.index', params));
