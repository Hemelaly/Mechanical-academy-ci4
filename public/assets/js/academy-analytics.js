/**
 * Mechanical Academy — analytics helpers (PostHog).
 *
 * Eventos standard para funis:
 *   course_view, checkout_view, payment_start, purchase, payment_failed,
 *   course_access, lesson_view, trial_click, login_view
 *
 * Uso: AcademyAnalytics.track('purchase', { course_id: 18, amount: 1500 })
 */
(function (window) {
  'use strict';

  function ready(fn) {
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', fn);
    } else {
      fn();
    }
  }

  function capture(event, props) {
    try {
      if (window.posthog && typeof window.posthog.capture === 'function') {
        window.posthog.capture(event, props || {});
        return true;
      }
    } catch (e) { /* silent */ }
    return false;
  }

  function pathName() {
    try {
      return String(window.location.pathname || '');
    } catch (e) {
      return '';
    }
  }

  function matchCourseId(path) {
    var m = path.match(/\/courses\/(\d+)(?:\/|$)/);
    return m ? Number(m[1]) : null;
  }

  function matchCheckoutId(path) {
    var m = path.match(/\/checkout\/(\d+)(?:\/|$)/);
    return m ? Number(m[1]) : null;
  }

  function matchLessonPlayer(path) {
    var m = path.match(/\/student\/dashboard\/ver_aulas\/(\d+)/);
    return m ? Number(m[1]) : null;
  }

  function matchLessonSlug(path) {
    // /student/dashboard/inscricoes/{courseSlug}/{lessonSlug}
    var m = path.match(/\/student\/dashboard\/inscricoes\/[^/]+\/[^/]+/);
    return !!m;
  }

  function fromDataLayer() {
    var root = document.body || document.documentElement;
    if (!root || !root.getAttribute) return {};
    var out = {};
    var courseId = root.getAttribute('data-analytics-course-id');
    var lessonId = root.getAttribute('data-analytics-lesson-id');
    var amount = root.getAttribute('data-analytics-amount');
    var title = root.getAttribute('data-analytics-course-title');
    if (courseId) out.course_id = Number(courseId);
    if (lessonId) out.lesson_id = Number(lessonId);
    if (amount) out.amount = Number(amount);
    if (title) out.course_title = title;
    return out;
  }

  function autoPageEvents() {
    var path = pathName();
    var base = fromDataLayer();
    var courseId;

    if (path.indexOf('/login') === 0 || path === '/login') {
      capture('login_view', { path: path });
      return;
    }

    courseId = matchCourseId(path);
    if (courseId && path.indexOf('/trial') === -1) {
      capture('course_view', Object.assign({ course_id: courseId, path: path }, base));
      return;
    }

    if (courseId && path.indexOf('/trial') !== -1) {
      capture('trial_click', Object.assign({ course_id: courseId, path: path }, base));
      return;
    }

    courseId = matchCheckoutId(path);
    if (courseId) {
      capture('checkout_view', Object.assign({ course_id: courseId, path: path }, base));
      return;
    }

    courseId = matchLessonPlayer(path);
    if (courseId) {
      capture('course_access', Object.assign({ course_id: courseId, path: path }, base));
      var lessonEl = document.getElementById('lesson-content');
      var lessonId = lessonEl ? Number(lessonEl.getAttribute('data-lesson-id') || 0) : 0;
      if (lessonId) {
        capture('lesson_view', Object.assign({
          course_id: courseId,
          lesson_id: lessonId,
          path: path
        }, base));
      }
      return;
    }

    if (matchLessonSlug(path)) {
      var lessonEl2 = document.getElementById('lesson-content');
      var lid = lessonEl2 ? Number(lessonEl2.getAttribute('data-lesson-id') || 0) : 0;
      capture('lesson_view', Object.assign({ lesson_id: lid || undefined, path: path }, base));
    }
  }

  var AcademyAnalytics = {
    track: function (event, props) {
      return capture(String(event || ''), props || {});
    },
    purchase: function (props) {
      return capture('purchase', Object.assign({ funnel: 'course_purchase' }, props || {}));
    },
    paymentStart: function (props) {
      return capture('payment_start', Object.assign({ funnel: 'course_purchase' }, props || {}));
    },
    paymentFailed: function (props) {
      return capture('payment_failed', Object.assign({ funnel: 'course_purchase' }, props || {}));
    }
  };

  window.AcademyAnalytics = AcademyAnalytics;

  // Esperar PostHog stub/init (array.js pode chegar depois)
  ready(function () {
    var tries = 0;
    var timer = setInterval(function () {
      tries += 1;
      if ((window.posthog && window.posthog.__loaded) || (window.posthog && typeof window.posthog.capture === 'function') || tries > 40) {
        clearInterval(timer);
        autoPageEvents();
      }
    }, 250);
  });
})(window);
