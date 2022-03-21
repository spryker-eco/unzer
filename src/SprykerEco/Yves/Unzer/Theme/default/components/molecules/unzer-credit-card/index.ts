import './styles.scss';
import register from 'ShopUi/app/registry';
export default register('unzer-credit-card', () => import(
    /* webpackMode: "lazy" */
    /* webpackChunkName: "unzer-credit-card" */
    './unzer-credit-card'));
