<?php

namespace App\Controllers;

use Framework\Database;
use Framework\Validation;
use Framework\Session;

class BookController
{
    protected $db;

    /**
     * Database Initialization
     * @return void
     */
    public function __construct()
    {
        $config = require_once basePath("config/db.php");
        $this->db = new Database($config);
    }

    /**
     * Issue a Book
     * @return void
     */
    public function issueBook()
    {
        $bookNo = $_POST["issue_book_no"];
        $memberEmail = $_POST["issue_member_email"];
        $inchargeId = Session::get("incharge")->Id;

        $transactions=[];
        $errors=[];

        if(!Validation::string($bookNo))
        {
            $errors["bookNo"] = "Invalid Book No. !!!";
        }
        if(!Validation::email($memberEmail))
        {
            $errors["memberEmail"] = "Invalid Email !!!";
        }

        if(!empty($errors))
        {
            $transactions = $this->db->query("SELECT * from transactions order by BorrowDate desc limit 5")->fetchAll();
            load("Incharge/Dashboard.incharge.transactions",[
                "issue_errors" => $errors,
                "issue_data" => [
                    "bookNo" => $bookNo,
                    "memberEmail" => $memberEmail
                ],
                "transactions" => $transactions
            ]);
            exit;
        }

        //-----Incharge Check
        $incharge = $this->db->query("SELECT * from incharge where Id = :inchargeId",["inchargeId" => $inchargeId])->fetch();
        if(!$incharge)
        {
            redirect("/incharge-dashboard");
        }

        //-----Member Check
        $member = $this->db->query("SELECT * from member where
        id = (SELECT MemberId from member_auth where Email = :memberEmail)
        ",["memberEmail" => $memberEmail])->fetch();
        if(!$member)
        {
            $errors["memberEmail"] = "This Email is not Registered !!!";
            $transactions = $this->db->query("SELECT * from transactions order by BorrowDate desc limit 5")->fetchAll();
            load("Incharge/Dashboard.incharge.transactions",[
                "issue_errors" => $errors,
                "issue_data" => [
                    "bookNo" => $bookNo,
                ],
                "transactions" => $transactions
            ]);
            exit;
        }

        //-----Book Check
        $book = $this->db->query("SELECT * from book_master where BookNo = :bookNo",["bookNo" => $bookNo])->fetch();
        if(!$book)
        {
            $errors["bookNo"] = "Invalid Book No. !!!";
            $transactions = $this->db->query("SELECT * from transactions order by BorrowDate desc limit 5")->fetchAll();
            load("Incharge/Dashboard.incharge.transactions",[
                "issue_errors" => $errors,
                "issue_data" => [
                    "memberEmail" => $memberEmail
                ],
                "transactions" => $transactions
            ]);
            exit;
        }

        if(strtoupper($book->Status) === "ISSUED")
        {
            $transactions = $this->db->query("SELECT * from transactions order by BorrowDate desc limit 5")->fetchAll();
            $errors["bookNo"] = "Book is not Available right now !!!";
            load("Incharge/Dashboard.incharge.transactions",[
                "issue_errors" => $errors,
                "issue_data" => [
                    "memberEmail" => $memberEmail
                ],
                "transactions" => $transactions
            ]);
            exit;
        }
        $params=[
            "bookNo" => $bookNo,
            "memberId" => $member->id,
            "inchargeId" => $inchargeId,
        ];
        //-----Insert
        $sql = "INSERT into transactions(BookNo, BorrowerId, LibrarianId) values(:bookNo, :memberId, :inchargeId)";
        $issue = $this->db->query($sql,$params);
        if($issue)
        {
            $update=$this->db->query("UPDATE book_master set Status = 'Issued' where BookNo = :bookNo",["bookNo" => $bookNo]);
            if($update)
            {
                redirect("/incharge-transactions",[
                    "issueSuccess" => "Book $bookNo issued by member $member->id Successfully !!!"
                ]);
            }
        }
    }

    /**
     * Return a Book
     * @return void
     */
    public function returnBook()
    {
        $bookNo = $_POST["return_book_no"];
        $memberEmail = $_POST["return_member_email"];
        $inchargeId = Session::get("incharge")->Id;

        $errors=[];
        $transactions=[];

        if(!Validation::string($bookNo))
        {
            $errors["bookNo"] = "Invalid Book No. !!!";
        }
        if(!Validation::email($memberEmail))
        {
            $errors["memberEmail"] = "Invalid Email !!!";
        }

        if(!empty($errors))
        {
            $transactions = $this->db->query("SELECT * from transactions order by BorrowDate desc limit 5")->fetchAll();
            load("Incharge/Dashboard.incharge.transactions",[
                "return_errors" => $errors,
                "return_data" => [
                    "bookNo" => $bookNo,
                    "memberEmail" => $memberEmail
                ],
                "transactions" => $transactions
            ]);
            exit;
        }

        //-----Incharge Check
        $incharge = $this->db->query("SELECT * from incharge where Id=:inchargeId",["inchargeId" => $inchargeId])->fetch();
        if(!$incharge)
        {
            redirect("/incharge-dashboard");
        }

        //-----Member Check
        $member = $this->db->query("SELECT * from member where
        id = (SELECT MemberId from member_auth where Email = :memberEmail)",["memberEmail" => $memberEmail])->fetch();
        if(!$member)
        {
            $errors["memberId"] = "This Email is not Registered !!!";
            $transactions = $this->db->query("SELECT * from transactions order by BorrowDate desc limit 5")->fetchAll();
            load("Incharge/Dashboard.incharge.transactions",[
                "return_errors" => $errors,
                "return_data" => [
                    "bookNo" => $bookNo,
                ],
                "transactions" => $transactions
            ]);
            exit;
        }

        //-----Book Check
        $book = $this->db->query("SELECT * from book_master where BookNo = :bookNo",["bookNo" => $bookNo])->fetch();
        if(!$book)
        {
            $errors["bookNo"] = "Invalid Book No. !!!";
            $transactions = $this->db->query("SELECT * from transactions order by BorrowDate desc limit 5")->fetchAll();
            load("Incharge/Dashboard.incharge.transactions",[
                "return_errors" => $errors,
                "return_data" => [
                    "memberEmail" => $memberEmail
                ],
                "transactions" => $transactions
            ]);
            exit;
        }

        if(strtoupper($book->Status) === "AVAILABLE")
        {
            $errors["bookNo"] = "Book is not Issued by any Member !!!";
            $transactions = $this->db->query("SELECT * from transactions order by BorrowDate desc limit 5")->fetchAll();
            load("Incharge/Dashboard.incharge.transactions",[
                "return_errors" => $errors,
                "return_data" => [
                    "memberEmail" => $memberEmail
                ],
                "transactions" => $transactions
            ]);
            exit;
        }
        $params=[
            "bookNo" => $bookNo,
            "memberId" => $member->id,
            "inchargeId" => $inchargeId
        ];
        $transaction = $this->db->query("SELECT * from transactions
        where ReturnDate is NULL and BookNo = :bookNo and 
        BorrowerId = :memberId and LibrarianId = :inchargeId",
        $params)->fetch();

        if($transaction)
        {
            $update = $this->db->query("UPDATE transactions set ReturnDate = CURRENT_TIMESTAMP
            where BookNo = :bookNo and BorrowerId = :memberId
            and LibrarianId = :inchargeId and ReturnDate is NULL",$params);

            if($update)
            {
                $update = $this->db->query("UPDATE book_master set Status = 'Available' where BookNo = :bookNo",["bookNo" => $bookNo]);
                if($update)
                {
                    redirect("/incharge-transactions",[
                        "returnSuccess" => "Book $bookNo returned by member $member->id Successfully !!!"
                    ]);
                }
            }
        }
        else
        {
            $transactions = $this->db->query("SELECT * from transactions order by BorrowDate desc limit 5")->fetchAll();
            $errors["bookNo"] = "This Book is not Issued by this Member !!!";
            load("Incharge/Dashboard.incharge.transactions",[
                "return_errors" => $errors,
                "return_data" => [
                    "memberEmail" => $memberEmail,
                    "bookNo" => $bookNo
                ],
                "transactions" => $transactions
            ]);
            exit;
        }
    }


    /**
     * Delete a Book
     * @return void
     */
    public function deleteBook(){
        if($_SERVER["REQUEST_METHOD"]=="POST"){
            $bookNo = $_POST["book_no"];
            $errors=[];
            $transactions=[];
    
            if(!Validation::string($bookNo))
            {
                $errors["bookNo"] = "Invalid Book No. !!!";
            }
    
            if(!empty($errors))
            {
                load("Incharge/Dashboard.incharge.DeleteBook",[
                    "delete_errors" => $errors,
                ]);
                exit;
            }
    
    
            //-----Book Check
            $book = $this->db->query("SELECT * from book_master where BookNo = :bookNo",["bookNo" => $bookNo])->fetch();
            if(!$book)
            {
                $errors["bookNo"] = "Invalid Book No. !!!";
                load("Incharge/Dashboard.incharge.DeleteBook",[
                    "delete_errors" => $errors,
                ]);
                exit;
            }
    
            if(strtoupper($book->Status) === "ISSUED")
            {
                $errors["bookNo"] = "Book is not Available right now !!!";
                load("Incharge/Dashboard.incharge.DeleteBook",[
                    "delete_errors" => $errors,
                ]);
                exit;
            }
    
            $delete = $this->db->query("DELETE from book_master where BookNo = :bookNo",["bookNo" => $bookNo]);
            if($delete)
            {
                load("Incharge/Dashboard.incharge.DeleteBook",[
                    "success" => "Book $bookNo Deleted Successfully !!!"
                ]);
                exit;
            }
        }
        load("Incharge/Dashboard.incharge.DeleteBook");
    }
}