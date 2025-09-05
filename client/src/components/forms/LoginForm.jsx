"use client"
import { useState } from "react"
import { useRouter } from "next/navigation"
import { loginAction } from "@/actions/authActions"
import { LoginButton } from "@/components/buttons/Buttons"
import styles from "./LoginForm.module.css"
import { DEFAULT_LOGIN_REDIRECT } from "@/route"

export default function LoginForm() {
  const [errors, setErrors] = useState({})
  const [success, setSuccess] = useState("")
  const router = useRouter()

  const handleSubmit = async (formData) => {
    setErrors({})
    setSuccess("")

    try {
      const result = await loginAction(formData)

      if (result.success) {
        setSuccess(result.success)
        // Redirect after successful login
        setTimeout(() => {
          router.push(DEFAULT_LOGIN_REDIRECT)
        }, 1500)
      } else if (result.error.email) {
        setErrors(result.error)
      } else {
        setErrors(result)
      }
    } catch (err) {
      setErrors({ error: "An unexpected error occurred. Please try again." })
    }
  }

  return (
    <div className={styles.formContainer}>
      <form action={handleSubmit} className={styles.loginForm}>
        <h2 className={styles.title}>Login</h2>

        {errors.email && <div className={styles.errorMessage}>{errors.email[0]}</div>}
        {errors.error && <div className={styles.errorMessage}>{errors.error}</div>}
        {success && <div className={styles.successMessage}>{success}</div>}

        <div className={styles.inputGroup}>
          <label htmlFor="email" className={styles.label}>
            Email
          </label>
          <input
            type="email"
            id="email"
            name="email"
            className={styles.input}
            required
          />
        </div>

        <div className={styles.inputGroup}>
          <label htmlFor="password" className={styles.label}>
            Password
          </label>
          <input
            type="password"
            id="password"
            name="password"
            className={styles.input}
            required
          />
        </div>

        <LoginButton />

        <div className={styles.loginLinkContainer}>
          Don't have an account? <a href="/auth/register" className={styles.loginLink}>Signup</a>
        </div>
      </form>
    </div>
  )
}
