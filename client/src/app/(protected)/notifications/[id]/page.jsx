"use client"

import { useState, useEffect } from "react"
import { useParams } from "next/navigation"
import { getNotificationAction, updateNotificationAction } from "@/actions/notificationActions"
import NotificationDetailCard from "@/components/cards/NotificationDetailCard"
import styles from "./page.module.css"

export default function NotificationDetailPage() {
  const params = useParams()
  const [notification, setNotification] = useState(null)
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState("")

  useEffect(() => {
    const fetchNotification = async () => {
      if (!params.id) return

      setLoading(true)
      setError("")

      try {
        const result = await getNotificationAction(params.id)

        if (result.error) {
          setError(result.error)
        } else {
          setNotification(result.data)

          // Mark as read if it's unread
          if (!result.data.is_read) {
            await updateNotificationAction(params.id)
            setNotification((prev) => ({ ...prev, is_read: true }))
          }
        }
      } catch (err) {
        setError("Failed to fetch notification")
      } finally {
        setLoading(false)
      }
    }

    fetchNotification()
  }, [params.id])

  if (loading) {
    return (
      <div className={styles.container}>
        <div className={styles.loading}>Loading notification...</div>
      </div>
    )
  }

  if (error) {
    return (
      <div className={styles.container}>
        <div className={styles.error}>{error}</div>
      </div>
    )
  }

  if (!notification) {
    return (
      <div className={styles.container}>
        <div className={styles.notFound}>Notification not found.</div>
      </div>
    )
  }

  return (
    <div className={styles.container}>
      <NotificationDetailCard notification={notification} />
    </div>
  )
}
