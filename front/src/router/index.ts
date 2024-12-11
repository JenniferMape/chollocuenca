import { createRouter, createWebHistory } from 'vue-router';
import isAuthenticatedGuard from '@/modules/auth/guards/is-authenticated.guard';
import NotFound404 from '@/modules/common/pages/NotFound404.vue';
import HomePage from '@/modules/landing/pages/HomePage.vue';
import LandingLayout from '@/modules/landing/layouts/LandingLayout.vue';
import AboutLayout from '@/modules/about/layouts/AboutLayout.vue';
import ContactPage from '@/modules/about/pages/ContactPage.vue';
import PrivacyPolicy from '@/modules/about/pages/PrivacyPolicy.vue';
import TermsOfUse from '@/modules/about/pages/TermsOfUse.vue';
import AboutUs from '@/modules/about/pages/AboutUs.vue';

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'landing',
      component: LandingLayout,
      children: [
        {
          path: '',
          name: 'home',
          component: HomePage,
        },
        {
          path: '/offer/:offerId?',
          name: 'OfferDetail',
          beforeEnter: [isAuthenticatedGuard],
          component: () => import('@/modules/offer/pages/offerView.vue'),
          props: (route) => ({
            offerId: Number(route.params.offerId),
          }),
        },
      ],
    },
    {
      path: '/about',
      name: 'about',
      component: AboutLayout,
      children: [
        {
          path: 'us',
          name: 'us',
          component: AboutUs,
        },
        {
          path: 'contact',
          name: 'contact',
          component: ContactPage,
        },
        {
          path: 'privacy',
          name: 'privacy',
          component: PrivacyPolicy,
        },
        {
          path: 'terms',
          name: 'terms',
          component: TermsOfUse,
        },
      ],
    },

    // Auth
    {
      path: '/auth',
      name: 'auth',
      redirect: { name: 'login' },
      component: () => import('@/modules/auth/layouts/AuthLayout.vue'),
      children: [
        {
          path: 'login',
          name: 'login',
          component: () => import('@/modules/auth/pages/LoginPage.vue'),
        },
        {
          path: 'reset',
          name: 'reset',
          component: () => import('@/modules/auth/pages/ResetPassword.vue'),
        },
        {
          path: 'register',
          name: 'register',
          component: () => import('@/modules/auth/pages/RegisterPage.vue'),
          beforeEnter: (to, from, next) => {
            const token = localStorage.getItem('token');

            if (token) {
              next({ name: 'home' }); // Redirige si está autenticado
            } else {
              next();
            }
          },
        },
      ],
    },
    // User
    {
      path: '/user',
      name: 'user',
      component: () => import('@/modules/user/layouts/UserLayout.vue'),
      children: [
        {
          path: 'clientProfile',
          name: 'clientProfile',
          beforeEnter: [isAuthenticatedGuard],
          component: () => import('@/modules/user/pages/clientPages/ClientProfilePage.vue'),
        },
        {
          path: 'favoritesOffers',
          name: 'favoritesOffers',
          beforeEnter: [isAuthenticatedGuard],
          component: () => import('@/modules/user/pages/clientPages/FavoritesOffers.vue'),
        },
        {
          path: 'companyProfile',
          name: 'companyProfile',
          beforeEnter: [isAuthenticatedGuard],
          component: () => import('@/modules/user/pages/companyPages/CompanyProfilePage.vue'),
        },
        {
          path: 'companyOffers',
          name: 'companyOffers',
          beforeEnter: [isAuthenticatedGuard],
          component: () => import('@/modules/user/pages/companyPages/CompanyOffers.vue'),
        },
        {
          path: '/offer/edit/:offerId?',
          name: 'formOffer',
          beforeEnter: [isAuthenticatedGuard],
          component: () => import('@/modules/user/pages/companyPages/EditOffers.vue'),
          props: (route) => ({
            offerId: route.params.offerId || null,
            isEditMode: !!route.params.offerId,
          }),
        },
      ],
    },

    // Not found
    {
      path: '/:pathMatch(.*)*',
      component: NotFound404,
    },
  ],
});

export default router;
