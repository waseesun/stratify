"use client"
import { useState } from "react"
import { useRouter } from "next/navigation"
import { loginAction } from "@/actions/authActions"
import { LoginButton } from "@/components/buttons/Buttons"
import styles from "./LoginForm.module.css"
import { DEFAULT_LOGIN_REDIRECT } from "@/route"

export default function LoginForm() {
  const [email, setEmail] = useState("")
  const [password, setPassword] = useState("")
  const [error, setError] = useState("")
  const [success, setSuccess] = useState("")
  const router = useRouter()

  const handleSubmit = async (formData) => {
    setError("")
    setSuccess("")

    try {
      const result = await loginAction(formData)

      if (result.success) {
        setSuccess(result.success)
        // Redirect after successful login
        setTimeout(() => {
          router.push(DEFAULT_LOGIN_REDIRECT)
        }, 1500)
      } else if (result.error) {
        setError(result.error)
      }
    } catch (err) {
      setError("An unexpected error occurred")
    }
  }

  return (
    <div className={styles.formContainer}>
      <form action={handleSubmit} className={styles.loginForm}>
        <h2 className={styles.title}>Login</h2>

        {error && <div className={styles.errorMessage}>{error}</div>}

        {success && <div className={styles.successMessage}>{success}</div>}

        <div className={styles.inputGroup}>
          <label htmlFor="email" className={styles.label}>
            Email
          </label>
          <input
            type="email"
            id="email"
            name="email"
            value={email}
            onChange={(e) => setEmail(e.target.value)}
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
            value={password}
            onChange={(e) => setPassword(e.target.value)}
            className={styles.input}
            required
          />
        </div>

        <LoginButton />
      </form>
    </div>
  )
}