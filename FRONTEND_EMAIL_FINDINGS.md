# Frontend Email Input Fields - Audit Report

## Summary
Comprehensive scan of the frontend codebase for email input field issues in forms.

---

## FINDINGS

### 1. ✅ Registration Form Email Field (LoginView.vue)
**Status:** No issues found

**Location:** [frontend/src/views/LoginView.vue](frontend/src/views/LoginView.vue#L295-L300)

**HTML:**
```vue
<div class="field">
  <label for="register-email">Email</label>
  <InputText
    id="register-email"
    v-model="registerForm.email"
    placeholder="you@company.com"
    autocomplete="email"
  />
</div>
```

**Assessment:**
- No `disabled` or `readonly` attributes
- Properly bound to `registerForm.email` via `v-model`
- Standard autocomplete attribute set correctly
- CSS styling allows normal interaction (no opacity: 0, no display: none)

---

### 2. ⚠️ Profile Form Email Field (ProfileView.vue)
**Status:** DISABLED - Intentional

**Location:** [frontend/src/views/ProfileView.vue](frontend/src/views/ProfileView.vue#L212-L216)

**HTML:**
```vue
<InputText
  id="profile-email"
  :modelValue="profile.email"
  disabled
/>
```

**Assessment:**
- Email field is **explicitly disabled** with `disabled` attribute
- Uses `:modelValue` (read-only) instead of `v-model`
- This appears intentional - users cannot edit their email in the profile page
- This is NOT the registration form, so not a problem for registration

---

### 3. ✅ Login Form Email Field (LoginView.vue)
**Status:** No issues found

**Location:** [frontend/src/views/LoginView.vue](frontend/src/views/LoginView.vue#L182-L188)

**HTML:**
```vue
<div class="field">
  <label for="login-email">Email</label>
  <InputText
    id="login-email"
    v-model="email"
    placeholder="you@company.com"
    autocomplete="email"
  />
</div>
```

**Assessment:**
- No disabling attributes
- Properly bound to reactive ref `email`
- CSS styling is normal

---

## CSS ANALYSIS

### Pseudo-elements with pointer-events: none (LoginView.vue)

Lines 459-460 and 473-475:
```css
.card-face::before {
  opacity: 0.04;
  pointer-events: none;  /* ← Applied to ::before pseudo-element */
}

.card-face::after {
  opacity: 0.01;
  pointer-events: none;  /* ← Applied to ::after pseudo-element */
}
```

**Assessment:** 
- These `pointer-events: none` rules are on pseudo-elements (::before and ::after), NOT on input fields
- They create decorative background effects and do NOT interfere with input functionality
- The form content has `z-index: 1` and is properly positioned above these effects

### CSS Animation/Transition Handling
Lines 820-821 (media query for reduced motion):
```css
@media (prefers-reduced-motion: reduce) {
  animation: none;
  transition: none;
}
```
**Assessment:** Standard accessibility best practice, no issues

---

## JAVASCRIPT ANALYSIS

### Registration Handler (LoginView.vue, Lines 118-132)
```javascript
const register = async () => {
  registerErrors.value = {}
  registerErrorMessage.value = ''
  registerSuccessMessage.value = ''
  registering.value = true

  try {
    await auth.register(registerForm)
    // ...
  } catch (error) {
    // ...
  }
}
```

**Assessment:**
- Email from `registerForm.email` is sent directly to backend
- No preprocessing or validation that would strip/modify email value
- No special handling that disables or interferes with the input field

---

## COMPREHENSIVE SEARCH RESULTS

### Disabled/Readonly Attributes
- **Total matches found:** 1
- **Location:** ProfileView.vue line 214 (profile email field - intentionally disabled)
- **Registration form email:** Not disabled ✅

### Pointer-events: none Patterns
- **Total matches found:** 6
  - 2x on `.card-face::before` and `.card-face::after` (decorative pseudo-elements)
  - 3x on `.auth-orb` and `.auth-wave` (background animations)
  - 1x in MainLayout sidebar (not related to forms)
- **Email inputs affected:** None ✅

### Opacity: 0 or Display: none on Inputs
- **Total matches found:** 0 on input fields
- **Note:** Opacity values found are only on pseudo-elements (0.04, 0.01, 0.7, etc.)
- **Email inputs affected:** None ✅

---

## CONCLUSION

✅ **No blocking issues found** in the registration form email field.

The registration form email input (`#register-email`) is:
- Fully functional and interactive
- Properly wired to component state
- Not affected by CSS that would disable/hide it
- Not affected by JavaScript that would interfere with input

**Possible issue areas to investigate if email still not working:**
1. Browser autocomplete interference
2. Backend validation/submission issues
3. API configuration (VITE_BACKEND_URL, VITE_API_URL)
4. PrimeVue InputText component override in node_modules
5. Browser console errors or network issues
6. Form submission handler (`register()` function)

---

## Files Scanned
- ✅ frontend/src/views/LoginView.vue
- ✅ frontend/src/views/ProfileView.vue
- ✅ frontend/src/views/ProjectView.vue
- ✅ frontend/src/views/ProjectsView.vue
- ✅ frontend/src/layouts/AuthLayout.vue
- ✅ frontend/src/layouts/MainLayout.vue
- ✅ frontend/src/components/layout/AppSidebar.vue
- ✅ frontend/src/assets/styles/main.scss
- ✅ frontend/src/App.vue
- ✅ frontend/src/main.js
- ✅ frontend/src/stores/auth.js

**Scan Date:** June 4, 2026
