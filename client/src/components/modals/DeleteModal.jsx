"use client"

import { useState } from "react"
import { useRouter } from "next/navigation"
import { deleteUserAction } from "@/actions/userActions"
import styles from "./DeleteModal.module.css"

export default function DeleteModal({ userId, onClose }) {
  const [isDeleting, setIsDeleting] = useState(false)
  const [error, setError] = useState("")
  const router = useRouter()

  const handleDelete = async () => {
    setIsDeleting(true)
    setError("")

    try {
      const result = await deleteUserAction(userId)

      if (result.error) {
        setError(result.error)
        setIsDeleting(false)
      } else if (result.success) {
        // Redirect to login page after successful deletion
        router.push("/login")
      }
    } catch (error) {
      setError("An unexpected error occurred")
      setIsDeleting(false)
    }
  }

  const handleBackdropClick = (e) => {
    if (e.target === e.currentTarget) {
      onClose()
    }
  }

  return (
    <div className={styles.backdrop} onClick={handleBackdropClick}>
      <div className={styles.modal}>
        <div className={styles.header}>
          <h2 className={styles.title}>Delete Account</h2>
          <button className={styles.closeButton} onClick={onClose}>
            ×
          </button>
        </div>

        <div className={styles.content}>
          <p className={styles.message}>
            Are you sure you want to delete your account? This action cannot be undone and will permanently remove all
            your data.
          </p>

          {error && <div className={styles.error}>{error}</div>}

          <div className={styles.actions}>
            <button className={styles.cancelButton} onClick={onClose} disabled={isDeleting}>
              No, Cancel
            </button>
            <button className={styles.deleteButton} onClick={handleDelete} disabled={isDeleting}>
              {isDeleting ? "Deleting..." : "Yes, Delete"}
            </button>
          </div>
        </div>
      </div>
    </div>
  )
}

