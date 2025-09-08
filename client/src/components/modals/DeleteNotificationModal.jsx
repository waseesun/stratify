"use client"

import { useState } from "react"
import { deleteNotificationAction } from "@/actions/notificationActions"
import styles from "./DeleteNotificationModal.module.css"

export default function DeleteNotificationModal({ notificationId, onClose, onSuccess }) {
  const [loading, setLoading] = useState(false)
  const [error, setError] = useState("")

  const handleDelete = async () => {
    setLoading(true)
    setError("")

    try {
      const result = await deleteNotificationAction(notificationId)

      if (result.error) {
        setError(result.error)
      } else {
        onSuccess()
      }
    } catch (err) {
      setError("Failed to delete notification")
    } finally {
      setLoading(false)
    }
  }

  const handleOverlayClick = (e) => {
    if (e.target === e.currentTarget) {
      onClose()
    }
  }

  return (
    <div className={styles.overlay} onClick={handleOverlayClick}>
      <div className={styles.modal}>
        <div className={styles.header}>
          <h3 className={styles.title}>Delete Notification</h3>
          <button className={styles.closeButton} onClick={onClose} disabled={loading}>
            ×
          </button>
        </div>

        <div className={styles.content}>
          <p className={styles.message}>
            Are you sure you want to delete this notification? This action cannot be undone.
          </p>

          {error && <div className={styles.error}>{error}</div>}
        </div>

        <div className={styles.actions}>
          <button className={styles.cancelButton} onClick={onClose} disabled={loading}>
            No, Cancel
          </button>
          <button className={styles.deleteButton} onClick={handleDelete} disabled={loading}>
            {loading ? "Deleting..." : "Yes, Delete"}
          </button>
        </div>
      </div>
    </div>
  )
}
