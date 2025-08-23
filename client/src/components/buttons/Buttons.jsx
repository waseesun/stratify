"use client"
import { useFormStatus } from "react-dom"
import styles from "./LoginButton.module.css"

export function LoginButton() {
  const { pending } = useFormStatus()

  return (
    <button type="submit" disabled={pending} className={styles.loginButton}>
      {pending ? "Logging in..." : "Login"}
    </button>
  )
}