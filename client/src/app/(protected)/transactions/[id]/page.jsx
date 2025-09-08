"use client"

import { useState, useEffect } from "react"
import { useParams } from "next/navigation"
import { getTransactionAction } from "@/actions/transactionActions"
import TransactionDetailCard from "@/components/cards/TransactionDetailCard"
import styles from "./page.module.css"

export default function TransactionDetailPage() {
  const params = useParams()
  const [transaction, setTransaction] = useState(null)
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState("")

  useEffect(() => {
    const fetchTransaction = async () => {
      if (!params.id) return

      setLoading(true)
      setError("")

      try {
        const result = await getTransactionAction(params.id)

        if (result.error) {
          setError(typeof result.error === "string" ? result.error : "Failed to load transaction")
        } else {
          setTransaction(result.data)
        }
      } catch (err) {
        setError("An unexpected error occurred")
      } finally {
        setLoading(false)
      }
    }

    fetchTransaction()
  }, [params.id])

  if (loading) {
    return (
      <div className={styles.container}>
        <div className={styles.loading}>Loading transaction...</div>
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

  if (!transaction) {
    return (
      <div className={styles.container}>
        <div className={styles.notFound}>Transaction not found</div>
      </div>
    )
  }

  return (
    <div className={styles.container}>
      <TransactionDetailCard transaction={transaction} />
    </div>
  )
}
