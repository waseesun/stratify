"use client"

import { useRouter } from "next/navigation"
import styles from "./NotificationCard.module.css"

export default function NotificationCard({ notification }) {
  const router = useRouter()

  const handleClick = () => {
    router.push(`/notifications/${notification.id}`)
  }

  const formatDate = (dateString) => {
    const date = new Date(dateString)
    return date.toLocaleDateString("en-US", {
      year: "numeric",
      month: "short",
      day: "numeric",
      hour: "2-digit",
      minute: "2-digit",
    })
  }

  const getTypeIcon = (type) => {
    switch (type) {
      case "problem":
        return "📋"
      case "transaction":
        return "💰"
      case "project":
        return "🚀"
      default:
        return "📢"
    }
  }

  return (
    <div className={`${styles.card} ${!notification.is_read ? styles.unread : ""}`} onClick={handleClick}>
      <div className={styles.header}>
        <div className={styles.typeIcon}>{getTypeIcon(notification.type)}</div>
        <div className={styles.type}>{notification.type.charAt(0).toUpperCase() + notification.type.slice(1)}</div>
        <div className={styles.date}>{formatDate(notification.created_at)}</div>
      </div>

      <div className={styles.content}>
        <p className={styles.message}>{notification.message}</p>
      </div>

      {!notification.is_read && (
        <div className={styles.unreadIndicator}>
          <span className={styles.unreadDot}></span>
          <span className={styles.unreadText}>New</span>
        </div>
      )}
    </div>
  )
}
