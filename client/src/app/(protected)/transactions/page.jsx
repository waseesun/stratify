"use client"

import { useState, useEffect } from "react"
import { getTransactionsAction } from "@/actions/transactionActions"
import { getUserRoleAction } from "@/actions/authActions"
import TransactionCard from "@/components/cards/TransactionCard"
import CreateTransactionButton from "@/components/buttons/CreateTransactionButton"
import CreateTransactionModal from "@/components/modals/CreateTransactionModal"
import Pagination from "@/components/pagination/Pagination"
import styles from "./page.module.css"

export default function TransactionsPage() {
  const [transactions, setTransactions] = useState([])
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState("")
  const [userRole, setUserRole] = useState(null)
  const [currentPage, setCurrentPage] = useState(1)
  const [totalPages, setTotalPages] = useState(1)
  const [isCreateModalOpen, setIsCreateModalOpen] = useState(false)

  const fetchTransactions = async (page = 1) => {
    setLoading(true)
    setError("")

    try {
      const result = await getTransactionsAction({ page })

      if (result.error) {
        setError(typeof result.error === "string" ? result.error : "Failed to load transactions")
      } else {
        setTransactions(result.data || [])
        setTotalPages(result.pagination?.total_pages || 1)
        setCurrentPage(page)
      }
    } catch (err) {
      setError("An unexpected error occurred")
    } finally {
      setLoading(false)
    }
  }

  const fetchUserRole = async () => {
    const result = await getUserRoleAction()
    if (result.error) {
      console.error("Failed to fetch user role:", result.error)
      return
    }

    setUserRole(result)
  }

  useEffect(() => {
    fetchUserRole()
    fetchTransactions(1)
  }, [])

  const handlePageChange = (page) => {
    fetchTransactions(page)
  }

  const handleTransactionCreated = () => {
    setIsCreateModalOpen(false)
    fetchTransactions(currentPage)
  }

  if (loading) {
    return (
      <div className={styles.container}>
        <div className={styles.loading}>Loading transactions...</div>
      </div>
    )
  }

  return (
    <div className={styles.container}>
      <div className={styles.header}>
        <h1 className={styles.title}>Transactions</h1>
        {userRole === "company" && (
          <CreateTransactionButton onClick={() => setIsCreateModalOpen(true)} />
        )}
      </div>

      {error && <div className={styles.error}>{error}</div>}

      {transactions.length === 0 ? (
        <div className={styles.noTransactions}>No transactions found.</div>
      ) : (
        <div className={styles.transactionsGrid}>
          {transactions.map((transaction) => (
            <TransactionCard key={transaction.id} transaction={transaction} />
          ))}
        </div>
      )}

      {totalPages > 1 && (
        <div className={styles.paginationContainer}>
          <Pagination currentPage={currentPage} totalPages={totalPages} onPageChange={handlePageChange} />
        </div>
      )}

      {isCreateModalOpen && (
        <CreateTransactionModal onClose={() => setIsCreateModalOpen(false)} onSuccess={handleTransactionCreated} />
      )}
    </div>
  )
}
