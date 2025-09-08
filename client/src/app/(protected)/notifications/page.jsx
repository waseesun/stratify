"use client"

import { useState, useEffect } from "react"
import { getNotificationsAction } from "@/actions/notificationActions"
import NotificationCard from "@/components/cards/NotificationCard"
import Pagination from "@/components/pagination/Pagination"
import styles from "./page.module.css"

export default function NotificationsPage() {
  const [notifications, setNotifications] = useState([])
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState("")
  const [currentPage, setCurrentPage] = useState(1)
  const [totalPages, setTotalPages] = useState(1)

  const fetchNotifications = async (page = 1) => {
    setLoading(true)
    setError("")

    try {
      const result = await getNotificationsAction({ page })

      if (result.error) {
        setError(result.error)
      } else {
        setNotifications(result.data || [])
        setTotalPages(result.pagination?.total_pages || 1)
        setCurrentPage(page)
      }
    } catch (err) {
      setError("Failed to fetch notifications")
    } finally {
      setLoading(false)
    }
  }

  useEffect(() => {
    fetchNotifications(1)
  }, [])

  const handlePageChange = (page) => {
    fetchNotifications(page)
  }

  if (loading) {
    return (
      <div className={styles.container}>
        <div className={styles.loading}>Loading notifications...</div>
      </div>
    )
  }

  return (
    <div className={styles.container}>
      <div className={styles.header}>
        <h1 className={styles.title}>Notifications</h1>
      </div>

      {error && <div className={styles.error}>{error}</div>}

      <div className={styles.notificationsGrid}>
        {notifications.length === 0 ? (
          <div className={styles.emptyState}>
            <p>No notifications found.</p>
          </div>
        ) : (
          notifications.map((notification) => <NotificationCard key={notification.id} notification={notification} />)
        )}
      </div>

      {totalPages > 1 && (
        <div className={styles.paginationContainer}>
          <Pagination currentPage={currentPage} totalPages={totalPages} onPageChange={handlePageChange} />
        </div>
      )}
    </div>
  )
}
