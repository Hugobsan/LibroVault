// 📌 Definições globais de entidades do LibroVault

export type UserRole = "admin" | "standard" | "plus";

// Enum para os status de processamento do Book (baseado no campo `processing_status`)
export type ProcessingStatus = "pending" | "processing" | "completed";

// Tipagem do Usuário
export interface User {
  id: number;
  name: string;
  email: string;
  role: UserRole; // Enum
  created_at: string;
  updated_at: string;
}

// Tipagem do Livro
export interface Book {
  id: number;
  user_id: number;
  title: string;
  volume?: number;
  edition?: string;
  pages: number;
  isbn?: string;
  author: string;
  genre: string;
  publisher?: string;
  description?: string;
  thumbnail?: string; // Será armazenado como um file_id no storage
  processing_status: ProcessingStatus; // Enum
  created_at: string;
  updated_at: string;
}

// Tipagem da Página do Livro
export interface BookPage {
  id: number;
  book_id: number;
  page_number: number;
  content: string;
  embedding_file: string; // Arquivo com os embeddings no storage
  created_at: string;
  updated_at: string;
}

// Tipagem de Arquivo
export interface File {
  id: number;
  user_id?: number; // Relacionado ao dono do arquivo
  path: string;
  mime_type: string;
  size: number;
  expires_in?: string; // Para arquivos temporários
  created_at: string;
  updated_at: string;
}